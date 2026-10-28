<?php

namespace App\Http\Controllers\TenantSite;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Services\OperatorPublicPackageCatalog;
use App\Services\PackageOfferPreviewPdfGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class PackageController extends Controller
{
    public function __construct(
        private readonly OperatorPublicPackageCatalog $packages,
        private readonly PackageOfferPreviewPdfGenerator $pdfGenerator,
    ) {}

    /**
     * Public sheet of one package, close to the agency preview.
     */
    public function show(Request $request, string $package): View
    {
        $account = $this->account($request);
        $catalog = $this->packages->find($account, $package);

        return view('site::package', [
            'account' => $account,
            'package' => $this->packages->sheet($account, $catalog),
        ]);
    }

    /**
     * Same sheet as a PDF, with the package photos.
     */
    public function pdf(Request $request, string $package): Response
    {
        $account = $this->account($request);
        $catalog = $this->packages->find($account, $package);
        $sheet = $this->packages->sheet($account, $catalog);

        $operatorName = trim((string) ($account->commercial_name ?? $account->name ?? $account->nick ?? ''));
        $filename = Str::slug(Str::limit((string) ($sheet['title'] ?? 'paquete'), 80, ''));
        if ($filename === '') {
            $filename = 'paquete';
        }

        /** @var array<string, mixed> $preview */
        $preview = $sheet['pdf_preview'] ?? [];

        return $this->pdfGenerator->downloadPreparedPreview(
            $preview,
            $filename.'-ficha',
            [
                'left' => $operatorName !== '' ? $operatorName : '—',
                'center' => 'Simple-Travel',
            ],
            true,
        );
    }

    private function account(Request $request): Account
    {
        $account = $request->attributes->get('tenant_website_account');
        abort_unless($account instanceof Account, 404);
        abort_unless(site_assets_published(), 404);

        return $account;
    }
}
