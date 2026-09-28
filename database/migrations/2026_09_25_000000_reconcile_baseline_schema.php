<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * B2 — Reconciliação do schema importado (database/havre_design.sql) com o
 * plano de backend.md §4 / laravel.md §6 (desvios 6.1 → 6.11).
 *
 * Regras:
 *  - Não recria as 7 tabelas do dump; só ALTER + tabelas em falta.
 *  - Todos os blocos têm guards (hasColumn/hasTable) para ser idempotente.
 *  - IDs UUID (VARCHAR(36) DEFAULT (UUID())) mantidos — 6.7.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->patchUsers();               // 6.6 + tabelas padrão (6.5)
        $this->patchAppointments();        // 6.1 + backend.md §4.9
        $this->patchServices();            // 6.3 (coluna group)
        $this->patchPortfolioItems();      // 6.9 (slug) + §4.5
        $this->createSolutions();          // 6.4
        $this->patchProjectRequests();     // 6.2
        $this->createAttachments();        // 6.4
        $this->createContactMessages();    // 6.4
        $this->createSettings();           // 6.4
        $this->createRedirects();          // 6.4
        $this->createTestimonials();       // backend.md §4.5
        $this->createAgendaSupport();      // availability_slots + appointment_blackouts (§4.9)
    }

    public function down(): void
    {
        // Reverter à baseline do dump: drop das tabelas novas e remoção das colunas acrescentadas.
        foreach (['appointment_blackouts', 'availability_slots', 'testimonials', 'redirects',
                  'settings', 'contact_messages', 'attachments', 'solutions'] as $table) {
            Schema::dropIfExists($table);
        }

        Schema::table('project_requests', function (Blueprint $t) {
            $t->dropForeign(['service_id']);
            $t->dropForeign(['solution_id']);
            foreach (['location', 'area_approx', 'project_stage', 'service_id', 'solution_id',
                      'segment', 'preferred_channel', 'preferred_time',
                      'privacy_consented_at', 'privacy_version', 'ip_address'] as $c) {
                if (Schema::hasColumn('project_requests', $c)) {
                    $t->dropColumn($c);
                }
            }
        });

        Schema::table('portfolio_items', function (Blueprint $t) {
            if (Schema::hasColumn('portfolio_items', 'slug')) {
                $t->dropUnique(['slug']);
                $t->dropColumn(['slug', 'status', 'segment']);
            }
        });

        Schema::table('services', function (Blueprint $t) {
            if (Schema::hasColumn('services', 'group')) {
                $t->dropColumn('group');
            }
        });

        Schema::table('appointments', function (Blueprint $t) {
            if (Schema::hasColumn('appointments', 'meeting_link')) {
                $t->dropUnique(['appt_date', 'appt_time']);
                $t->dropColumn(['meeting_link', 'timezone', 'confirmed_at', 'cancelled_at', 'fee_note']);
            }
            $t->enum('type', ['OFFICE', 'ON_SITE'])->default('OFFICE')->change();
        });

        Schema::table('users', function (Blueprint $t) {
            foreach (['phone', 'email_verified_at', 'remember_token'] as $c) {
                if (Schema::hasColumn('users', $c)) {
                    $t->dropColumn($c);
                }
            }
        });

        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
    }

    private function patchUsers(): void
    {
        Schema::table('users', function (Blueprint $t) {
            if (! Schema::hasColumn('users', 'phone')) {
                $t->string('phone', 30)->nullable()->after('role');
            }
            if (! Schema::hasColumn('users', 'email_verified_at')) {
                $t->timestamp('email_verified_at')->nullable()->after('phone');
            }
            if (! Schema::hasColumn('users', 'remember_token')) {
                $t->string('remember_token', 100)->nullable()->after('email_verified_at');
            }
        });

        if (! Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $t) {
                $t->string('email')->primary();
                // A coluna é `token` — é a que o DatabaseTokenRepository do Laravel lê/escreve.
                $t->string('token');
                $t->timestamp('created_at')->nullable();
            });
        }

        if (! Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $t) {
                $t->string('id')->primary();
                $t->string('user_id', 36)->nullable()->index();
                $t->string('ip_address', 45)->nullable();
                $t->text('user_agent')->nullable();
                $t->longText('payload');
                $t->integer('last_activity')->index();
            });
        }
    }

    private function patchAppointments(): void
    {
        // 6.1 — o site usa OFFICE / SITE / ONLINE (AGENDA_CONFIG em ui/js/app.js).
        // Só faz sentido no MySQL do dump: noutros drivers o `type` já nasce certo
        // (2026_09_24_000000) e um ->change() em sqlite reconstrói a tabela à toa.
        if (DB::getDriverName() === 'mysql' && Schema::hasTable('appointments')) {
            $type = Schema::getColumns('appointments')['type']['type'] ?? '';
            if ($type !== "enum('OFFICE','SITE','ONLINE')") {
                Schema::table('appointments', function (Blueprint $t) {
                    $t->enum('type', ['OFFICE', 'SITE', 'ONLINE'])->default('OFFICE')->change();
                });
            }
        }

        if (! Schema::hasTable('appointments')) {
            return;
        }

        Schema::table('appointments', function (Blueprint $t) {
            if (! Schema::hasColumn('appointments', 'meeting_link')) {
                $t->string('meeting_link', 500)->nullable()->after('address');
                $t->string('timezone', 60)->default('Africa/Luanda')->after('notes');
                $t->timestamp('confirmed_at')->nullable()->after('status');
                $t->timestamp('cancelled_at')->nullable()->after('confirmed_at');
                $t->text('fee_note')->nullable()->after('cancelled_at');
                // Secção 7: impedir marcações duplicadas no mesmo dia/hora.
                $t->unique(['appt_date', 'appt_time']);
            }
        });
    }

    private function patchServices(): void
    {
        Schema::table('services', function (Blueprint $t) {
            if (! Schema::hasColumn('services', 'group')) {
                $t->enum('group', ['principal', 'complementar'])->default('principal')->after('id');
            }
        });
    }

    private function patchPortfolioItems(): void
    {
        Schema::table('portfolio_items', function (Blueprint $t) {
            if (! Schema::hasColumn('portfolio_items', 'slug')) {
                $t->string('slug', 200)->nullable()->after('title');
                $t->unique('slug');
            }
            if (! Schema::hasColumn('portfolio_items', 'status')) {
                $t->enum('status', ['draft', 'published'])->default('published')->after('category');
            }
            if (! Schema::hasColumn('portfolio_items', 'segment')) {
                $t->enum('segment', [
                    'Investimento imobiliário residencial',
                    'Habitação própria',
                    'Comércio e serviços',
                    'Outro',
                ])->nullable()->after('status');
            }
        });
    }

    private function createSolutions(): void
    {
        if (Schema::hasTable('solutions')) {
            return;
        }

        Schema::create('solutions', function (Blueprint $t) {
            $t->string('id', 36)->primary();
            $t->string('code', 20)->unique();
            $t->string('name', 120);
            $t->string('slug', 120)->unique();
            $t->text('description');
            $t->string('cta_label', 60)->nullable();
            $t->integer('sort_order')->default(0);
            $t->boolean('active')->default(true);
            $t->timestamps();

            $t->index('active');
        });
    }

    private function patchProjectRequests(): void
    {
        Schema::table('project_requests', function (Blueprint $t) {
            if (! Schema::hasColumn('project_requests', 'location')) {
                $t->string('location', 255)->nullable()->after('address');
                $t->string('area_approx', 50)->nullable()->after('location');
                $t->string('project_stage', 60)->nullable()->after('area_approx');
                $t->string('service_id', 36)->nullable()->after('project_stage');
                $t->string('solution_id', 36)->nullable()->after('service_id');
                $t->enum('segment', [
                    'Investimento imobiliário residencial',
                    'Habitação própria',
                    'Comércio e serviços',
                    'Outro',
                ])->nullable()->after('solution_id');
                $t->enum('preferred_channel', ['email', 'phone', 'whatsapp'])->default('email')->after('segment');
                $t->string('preferred_time', 60)->nullable()->after('preferred_channel');
                $t->timestamp('privacy_consented_at')->nullable()->after('status');
                $t->string('privacy_version', 20)->nullable()->after('privacy_consented_at');
                $t->string('ip_address', 45)->nullable()->after('privacy_version');

                $t->foreign('service_id')->references('id')->on('services')->nullOnDelete();
                $t->foreign('solution_id')->references('id')->on('solutions')->nullOnDelete();
            }
        });
    }

    private function createAttachments(): void
    {
        if (Schema::hasTable('attachments')) {
            return;
        }

        Schema::create('attachments', function (Blueprint $t) {
            $t->string('id', 36)->primary();
            $t->string('request_id', 36);
            $t->string('original_name', 255);
            $t->string('path', 500);
            $t->string('mime_type', 100);
            $t->unsignedBigInteger('size_bytes');
            $t->timestamp('created_at')->nullable();

            $t->foreign('request_id')->references('id')->on('project_requests')->cascadeOnDelete();
            $t->index('request_id');
        });
    }

    private function createContactMessages(): void
    {
        if (Schema::hasTable('contact_messages')) {
            return;
        }

        Schema::create('contact_messages', function (Blueprint $t) {
            $t->string('id', 36)->primary();
            $t->string('name', 150);
            $t->string('email', 255);
            $t->string('phone', 30)->nullable();
            $t->string('subject', 200)->nullable();
            $t->text('message');
            $t->timestamp('privacy_consented_at')->nullable();
            $t->enum('status', ['new', 'replied', 'closed'])->default('new');
            $t->string('ip_address', 45)->nullable();
            $t->timestamp('created_at')->nullable();

            $t->index('status');
        });
    }

    private function createSettings(): void
    {
        if (Schema::hasTable('settings')) {
            return;
        }

        Schema::create('settings', function (Blueprint $t) {
            $t->string('id', 36)->primary();
            $t->string('key', 150)->unique();
            $t->text('value');
            $t->string('group', 60)->default('general');
            $t->timestamps();
        });
    }

    private function createRedirects(): void
    {
        if (Schema::hasTable('redirects')) {
            return;
        }

        Schema::create('redirects', function (Blueprint $t) {
            $t->string('id', 36)->primary();
            $t->string('source_path', 255)->unique();
            $t->string('target_path', 255);
            $t->unsignedSmallInteger('status_code')->default(301);
            $t->boolean('active')->default(true);
            $t->timestamps();
        });
    }

    private function createTestimonials(): void
    {
        if (Schema::hasTable('testimonials')) {
            return;
        }

        Schema::create('testimonials', function (Blueprint $t) {
            $t->string('id', 36)->primary();
            $t->string('name', 150);
            $t->string('role', 150)->nullable();
            $t->text('content');
            $t->enum('status', ['hidden', 'published'])->default('hidden');
            $t->integer('sort_order')->default(0);
            $t->timestamps();

            $t->index('status');
        });
    }

    private function createAgendaSupport(): void
    {
        if (! Schema::hasTable('availability_slots')) {
            Schema::create('availability_slots', function (Blueprint $t) {
                $t->string('id', 36)->primary();
                $t->unsignedTinyInteger('weekday'); // 0 = domingo … 6 = sábado
                $t->time('start_time');
                $t->time('end_time');
                $t->unsignedInteger('slot_minutes')->default(60);
                $t->boolean('active')->default(true);
                $t->timestamps();

                $t->unique(['weekday', 'start_time']);
            });
        }

        if (! Schema::hasTable('appointment_blackouts')) {
            Schema::create('appointment_blackouts', function (Blueprint $t) {
                $t->string('id', 36)->primary();
                $t->date('date');
                $t->string('reason', 255)->nullable();
                $t->timestamps();

                $t->unique('date');
            });
        }
    }
};
