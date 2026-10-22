<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Catalog help for the service wizard step 2 "status" field.
     *
     * Helper identity: screen_code = service_wizard_step2, code = status
     * (generic: applies to every service type and account type).
     */
    public function up(): void
    {
        $helperId = DB::table('cat_helpers')
            ->where('screen_code', 'service_wizard_step2')
            ->where('code', 'status')
            ->whereNull('account_type_id')
            ->whereNull('service_type_id')
            ->value('id');

        if ($helperId === null) {
            $helperId = DB::table('cat_helpers')->insertGetId([
                'screen_code' => 'service_wizard_step2',
                'code' => 'status',
                'account_type_id' => null,
                'service_type_id' => null,
                'active' => true,
                'notes' => null,
            ]);
        }

        foreach ($this->texts() as $languageId => $text) {
            DB::table('cat_helper_translations')->updateOrInsert(
                ['helper_id' => $helperId, 'language_id' => $languageId],
                ['text' => $text],
            );
        }
    }

    public function down(): void
    {
        $helperId = DB::table('cat_helpers')
            ->where('screen_code', 'service_wizard_step2')
            ->where('code', 'status')
            ->whereNull('account_type_id')
            ->whereNull('service_type_id')
            ->value('id');

        if ($helperId === null) {
            return;
        }

        DB::table('cat_helper_translations')->where('helper_id', $helperId)->delete();
        DB::table('cat_helpers')->where('id', $helperId)->delete();
    }

    /**
     * Help texts by language id (1=en, 2=es, 3=pt).
     *
     * @return array<int, string>
     */
    private function texts(): array
    {
        return [
            1 => '<p>The status controls whether this service can be offered and sold:</p>'
                .'<ul>'
                .'<li><strong>Active:</strong> the service is enabled. Only active services (with active variants) can be offered to operators and sold.</li>'
                .'<li><strong>On hold:</strong> the initial status while you complete the wizard. It is not offered or sold.</li>'
                .'<li><strong>Suspended:</strong> a temporary pause. It is not offered or sold, but you can reactivate it at any time.</li>'
                .'<li><strong>Discontinued:</strong> the service is no longer offered for good; it is left out of the pickers used to offer services to operators. Its history is kept.</li>'
                .'<li><strong>Inactive:</strong> the service is switched off, with no commercial effect. Similar to suspended, for services not currently in use.</li>'
                .'<li><strong>Terminated:</strong> a definitive closure. On top of not being sold, it disappears from the usual catalog list (it only shows when filtering by this status).</li>'
                .'</ul>'
                .'<p>Suspending or terminating a service does not delete it or its history.</p>',
            2 => '<p>El estado define si el servicio puede ofrecerse y venderse:</p>'
                .'<ul>'
                .'<li><strong>Activo:</strong> el servicio está habilitado. Solo los servicios activos (con variantes activas) pueden ofrecerse a operadores y venderse.</li>'
                .'<li><strong>En espera:</strong> estado inicial mientras completas el asistente. No se ofrece ni se vende.</li>'
                .'<li><strong>Suspendido:</strong> pausa temporal. No se ofrece ni se vende, pero puedes reactivarlo en cualquier momento.</li>'
                .'<li><strong>Descontinuado:</strong> el servicio dejó de ofrecerse de forma definitiva; queda fuera de los selectores para ofrecer servicios a operadores. Se conserva el historial.</li>'
                .'<li><strong>Inactivo:</strong> servicio apagado, sin efecto comercial. Similar a suspendido, para servicios que no están en uso.</li>'
                .'<li><strong>Dado de baja:</strong> baja definitiva. Además de no venderse, desaparece del listado habitual del catálogo (solo se ve al filtrar por este estado).</li>'
                .'</ul>'
                .'<p>Suspender o dar de baja un servicio no lo borra ni elimina su historial.</p>',
            3 => '<p>O status define se o serviço pode ser oferecido e vendido:</p>'
                .'<ul>'
                .'<li><strong>Ativo:</strong> o serviço está habilitado. Somente serviços ativos (com variantes ativas) podem ser oferecidos a operadores e vendidos.</li>'
                .'<li><strong>Em espera:</strong> status inicial enquanto você conclui o assistente. Não é oferecido nem vendido.</li>'
                .'<li><strong>Suspenso:</strong> pausa temporária. Não é oferecido nem vendido, mas você pode reativá-lo a qualquer momento.</li>'
                .'<li><strong>Descontinuado:</strong> o serviço deixou de ser oferecido de forma definitiva; fica fora dos seletores para oferecer serviços a operadores. O histórico é mantido.</li>'
                .'<li><strong>Inativo:</strong> serviço desligado, sem efeito comercial. Semelhante a suspenso, para serviços que não estão em uso.</li>'
                .'<li><strong>Encerrado:</strong> baixa definitiva. Além de não ser vendido, desaparece da lista habitual do catálogo (só aparece ao filtrar por este status).</li>'
                .'</ul>'
                .'<p>Suspender ou encerrar um serviço não o exclui nem apaga seu histórico.</p>',
        ];
    }
};
