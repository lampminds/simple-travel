<?php

namespace App\Http\Controllers;

use App\Filament\Pages\TicketsUnavailablePage;
use App\Support\KbPortal;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;

/**
 * Exchanges the signed-in user for a one-time KB portal URL and sends them there.
 */
class OpenKbPortalController extends Controller
{
    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function __invoke(): RedirectResponse
    {
        if (! KbPortal::isConfigured()) {
            return redirect()->to(TicketsUnavailablePage::getUrl(panel: 'smpl_adm'));
        }

        $user = auth()->user();

        $response = Http::withToken(config('services.kb.key'))
            ->acceptJson()
            ->timeout(10)
            ->post(rtrim((string) config('services.kb.url'), '/').'/api/portal/launch', [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ]);

        $response->throw();

        return redirect()->away($response->json('url'));
    }
}
