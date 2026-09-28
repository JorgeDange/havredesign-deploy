<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * B12 — tabelas da baseline (database/havre_design.sql) quando não existem.
 *
 * A migration `2026_09_25_000000_reconcile_baseline_schema` só faz ALTER/CREATE
 * condicional: se a BD chega sem o dump importado (testes `sqlite::memory:`,
 * `migrate:fresh` num servidor novo) rebenta com «no such table».
 *
 * Esta migration corre **antes** dela e cria as 6 tabelas do dump + `solutions`
 * já na forma final (dump + §6 da laravel.md). Nos servidores com o dump
 * importado tudo já existe → nenhum bloco corre, e os guards da reconcile
 * continuam a ser no-ops. Forma copiada de `SHOW CREATE TABLE` da BD real.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->createServices();
        $this->createServiceIncludes();
        $this->createSolutions();
        $this->createPortfolioItems();
        $this->createPortfolioGallery();
        $this->createAppointments();
        $this->createProjectRequests();
    }

    public function down(): void
    {
        foreach (['project_requests', 'appointments', 'portfolio_gallery', 'portfolio_items',
                  'solutions', 'service_includes', 'services'] as $table) {
            Schema::dropIfExists($table);
        }
    }

    private function createServices(): void
    {
        if (Schema::hasTable('services')) {
            return;
        }

        Schema::create('services', function (Blueprint $t) {
            $t->string('id', 36)->primary();
            $t->enum('group', ['principal', 'complementar'])->default('principal');
            $t->string('title', 200);
            $t->string('slug', 200)->unique();
            $t->text('description');
            $t->string('icon', 50)->default('home');
            $t->string('image_url', 500)->nullable();
            $t->boolean('active')->default(true);
            $t->integer('sort_order')->default(0);
            $t->timestamps();

            $t->index('active', 'idx_services_active');
            $t->index('sort_order', 'idx_services_order');
        });
    }

    private function createServiceIncludes(): void
    {
        if (Schema::hasTable('service_includes')) {
            return;
        }

        Schema::create('service_includes', function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->string('service_id', 36);
            $t->string('item', 500);
            $t->integer('sort_order')->default(0);

            $t->index('service_id', 'idx_si_service');
            $t->foreign('service_id', 'fk_si_service')
                ->references('id')->on('services')
                ->cascadeOnDelete()->cascadeOnUpdate();
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

    private function createPortfolioItems(): void
    {
        if (Schema::hasTable('portfolio_items')) {
            return;
        }

        Schema::create('portfolio_items', function (Blueprint $t) {
            $t->string('id', 36)->primary();
            $t->string('title', 200);
            $t->string('slug', 200)->nullable()->unique();
            $t->enum('category', ['Residencial', 'Comercial', 'Corporativo', 'Outro'])->default('Outro');
            $t->enum('status', ['draft', 'published'])->default('published');
            $t->enum('segment', [
                'Investimento imobiliário residencial',
                'Habitação própria',
                'Comércio e serviços',
                'Outro',
            ])->nullable();
            $t->text('description');
            $t->string('area', 50)->nullable();
            $t->year('year')->nullable();
            $t->string('location', 255)->nullable();
            $t->string('image_url', 500)->nullable();
            $t->boolean('featured')->default(false);
            $t->integer('sort_order')->default(0);
            $t->timestamps();

            $t->index('category', 'idx_portfolio_category');
            $t->index('featured', 'idx_portfolio_featured');
            $t->index('sort_order', 'idx_portfolio_order');
        });
    }

    private function createPortfolioGallery(): void
    {
        if (Schema::hasTable('portfolio_gallery')) {
            return;
        }

        Schema::create('portfolio_gallery', function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->string('portfolio_id', 36);
            $t->string('image_url', 500);
            $t->integer('sort_order')->default(0);

            $t->index('portfolio_id', 'idx_pg_portfolio');
            $t->foreign('portfolio_id', 'fk_pg_portfolio')
                ->references('id')->on('portfolio_items')
                ->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    private function createAppointments(): void
    {
        if (Schema::hasTable('appointments')) {
            return;
        }

        Schema::create('appointments', function (Blueprint $t) {
            $t->string('id', 36)->primary();
            $t->string('user_id', 36)->nullable();
            $t->string('user_name', 150);
            $t->string('user_email', 255);
            $t->string('user_phone', 30);
            $t->enum('type', ['OFFICE', 'SITE', 'ONLINE'])->default('OFFICE');
            $t->string('address', 500)->nullable();
            $t->string('meeting_link', 500)->nullable();
            $t->date('appt_date');
            $t->time('appt_time');
            $t->text('notes')->nullable();
            $t->string('timezone', 60)->default('Africa/Luanda');
            $t->enum('status', ['PENDING', 'CONFIRMED', 'CANCELLED'])->default('PENDING');
            $t->timestamp('confirmed_at')->nullable();
            $t->timestamp('cancelled_at')->nullable();
            $t->text('fee_note')->nullable();
            $t->timestamps();

            $t->unique(['appt_date', 'appt_time']);
            $t->index('user_id', 'idx_apt_user');
            $t->index('status', 'idx_apt_status');
            $t->index('appt_date', 'idx_apt_date');
            $t->foreign('user_id', 'fk_apt_user')
                ->references('id')->on('users')
                ->nullOnDelete()->cascadeOnUpdate();
        });
    }

    private function createProjectRequests(): void
    {
        if (Schema::hasTable('project_requests')) {
            return;
        }

        Schema::create('project_requests', function (Blueprint $t) {
            $t->string('id', 36)->primary();
            $t->string('user_id', 36)->nullable();
            $t->string('user_name', 150);
            $t->string('user_email', 255);
            $t->string('user_phone', 30);
            $t->string('address', 500)->nullable();
            $t->string('location', 255)->nullable();
            $t->string('area_approx', 50)->nullable();
            $t->string('project_stage', 60)->nullable();
            $t->string('service_id', 36)->nullable();
            $t->string('solution_id', 36)->nullable();
            $t->enum('segment', [
                'Investimento imobiliário residencial',
                'Habitação própria',
                'Comércio e serviços',
                'Outro',
            ])->nullable();
            $t->enum('preferred_channel', ['email', 'phone', 'whatsapp'])->default('email');
            $t->string('preferred_time', 60)->nullable();
            $t->string('project_type', 100);
            $t->string('budget', 100)->nullable();
            $t->string('timeline', 100)->nullable();
            $t->text('description');
            $t->enum('status', ['NEW', 'IN_REVIEW', 'APPROVED', 'IN_PROGRESS', 'COMPLETED', 'REJECTED'])->default('NEW');
            $t->timestamp('privacy_consented_at')->nullable();
            $t->string('privacy_version', 20)->nullable();
            $t->string('ip_address', 45)->nullable();
            $t->timestamps();

            $t->index('user_id', 'idx_pr_user');
            $t->index('status', 'idx_pr_status');
            $t->index('project_type', 'idx_pr_type');
            $t->foreign('user_id', 'fk_pr_user')
                ->references('id')->on('users')
                ->nullOnDelete()->cascadeOnUpdate();
            $t->foreign('service_id')->references('id')->on('services')->nullOnDelete();
            $t->foreign('solution_id')->references('id')->on('solutions')->nullOnDelete();
        });
    }
};
