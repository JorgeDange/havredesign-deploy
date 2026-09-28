<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * FASE 3 — remoção da promessa de atendimento em escritório.
 *
 * 1. `appointments.type` deixa de aceitar OFFICE (fica só SITE / ONLINE, default ONLINE).
 * 2. `settings.agenda` perde o tipo OFFICE, passa `tipo_predefinido` a ONLINE e
 *    recebe a nota final de ONLINE (a de SITE já era a aprovada).
 * 3. Nova chave `process.case_local` — o "Local" do case study da página Processo,
 *    passa a ser editável no painel (Definições → Caso Real), mantendo o texto atual.
 *
 * Idempotente, com guards de driver: o ->change() de um enum só corre em MySQL,
 * pela mesma regra de 2026_09_25_000000 (em sqlite reconstrói a tabela à toa).
 * Nunca edita migrations anteriores.
 */
return new class extends Migration
{
    /** Copy aprovada: nota da reunião online. */
    private const NOTA_ONLINE = 'Conversa por videochamada ou telefone, no horário combinado.';

    /** Copy aprovada: nota da visita ao local (já existia, mantida tal e qual). */
    private const NOTA_SITE = 'A visita ao local pode implicar uma taxa de deslocação, calculada conforme a localização e confirmada previamente.';

    /** Texto atual do case study — fica como omissão até ser alterado no painel. */
    private const CASE_LOCAL = 'Benfica, Via Expressa, Bairro Tchinguari, Rua 1, Talatona, Luanda';

    private const NOTA_ONLINE_ANTIGA = 'Sem deslocação';

    public function up(): void
    {
        $this->seedCaseLocal();
        $this->patchAgenda();
        $this->narrowAppointmentType();
    }

    public function down(): void
    {
        if (Schema::hasTable('settings')) {
            $agenda = Setting::get('agenda');
            if (is_array($agenda)) {
                $agenda['tipos']['OFFICE'] = ['label' => 'Reunião no Escritório', 'precisa_endereco' => false];
                $agenda['tipo_predefinido'] = 'OFFICE';
                Setting::set('agenda', $agenda, 'agenda');
            }

            Setting::query()->where('key', 'process.case_local')->delete();
            Cache::forget('setting:process.case_local');
        }

        if (DB::getDriverName() === 'mysql' && Schema::hasTable('appointments')) {
            Schema::table('appointments', function (Blueprint $t) {
                $t->enum('type', ['OFFICE', 'SITE', 'ONLINE'])->default('OFFICE')->change();
            });
        }
    }

    private function seedCaseLocal(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        if (Setting::get('process.case_local') === null) {
            Setting::set('process.case_local', self::CASE_LOCAL, 'process');
        }
    }

    private function patchAgenda(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        $agenda = Setting::get('agenda');
        if (! is_array($agenda) || $agenda === []) {
            return;
        }

        unset($agenda['tipos']['OFFICE']);

        $agenda['tipo_predefinido'] = 'ONLINE';

        $agenda['tipos']['SITE']['label'] ??= 'Visita ao Local';
        $agenda['tipos']['SITE']['precisa_endereco'] = true;
        $agenda['tipos']['SITE']['nota'] ??= self::NOTA_SITE;

        $agenda['tipos']['ONLINE']['label'] ??= 'Reunião Online';
        $agenda['tipos']['ONLINE']['precisa_endereco'] = false;

        // Só substitui a nota antiga — não cobra uma edição já feita no painel.
        $nota = $agenda['tipos']['ONLINE']['nota'] ?? null;
        if ($nota === null || $nota === self::NOTA_ONLINE_ANTIGA) {
            $agenda['tipos']['ONLINE']['nota'] = self::NOTA_ONLINE;
        }

        Setting::set('agenda', $agenda, 'agenda');
    }

    private function narrowAppointmentType(): void
    {
        if (! Schema::hasTable('appointments')) {
            return;
        }

        // Nenhum registo pode ficar fora do novo enum.
        DB::table('appointments')->where('type', 'OFFICE')->update(['type' => 'ONLINE']);

        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $tipo = Schema::getColumns('appointments')['type']['type'] ?? '';
        if ($tipo === "enum('SITE','ONLINE')") {
            return;
        }

        Schema::table('appointments', function (Blueprint $t) {
            $t->enum('type', ['SITE', 'ONLINE'])->default('ONLINE')->change();
        });
    }
};
