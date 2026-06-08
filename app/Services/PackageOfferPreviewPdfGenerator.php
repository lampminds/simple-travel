<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Account;
use App\Models\PackageOffer;
use App\Models\User;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Renders the agency package-offer preview sheet as a downloadable PDF.
 */
final class PackageOfferPreviewPdfGenerator
{
    public function __construct(
        private readonly PackageOfferPreviewBuilder $previewBuilder,
        private readonly OperatorPackageAgencyPriceResolver $packagePriceResolver,
    ) {
    }

    public function downloadResponse(PackageOffer $offer, bool $includePhotos = true): Response
    {
        $offer->loadMissing(['catalog', 'priceList']);
        abort_unless($offer->catalog !== null, 404);

        $agencyPrice = null;
        if ($offer->priceList !== null) {
            $agencyPrice = $this->packagePriceResolver->resolvePackageTotal(
                $offer->catalog,
                $offer->priceList,
                (int) $offer->agency_id,
                (int) $offer->operator_id,
            );
        }

        $preview = $this->previewBuilder->build($offer, $agencyPrice);

        if ($includePhotos) {
            $preview = $this->embedPreviewMedia($preview);
        } else {
            $preview = $this->stripPreviewMedia($preview);
        }

        $html = view('account.package-offers.agency.preview-pdf', [
            'preview' => $preview,
        ])->render();

        $options = new Options;
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');
        $options->setChroot([
            public_path(),
            storage_path('app/public'),
            storage_path('app/public/service-media'),
        ]);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $this->applyFooter($dompdf, $this->footerMeta());

        $filename = Str::slug(Str::limit((string) ($preview['title'] ?? 'package'), 80, ''));
        if ($filename === '') {
            $filename = 'package-offer-'.($offer->uuid ?: $offer->id);
        }

        $filenameSuffix = $includePhotos ? '-ficha' : '-ficha-sin-fotos';

        return response()->streamDownload(
            static function () use ($dompdf): void {
                echo $dompdf->output();
            },
            $filename.$filenameSuffix.'.pdf',
            ['Content-Type' => 'application/pdf'],
        );
    }

    /**
     * @param  array<string, mixed>  $preview
     * @return array<string, mixed>
     */
    private function stripPreviewMedia(array $preview): array
    {
        $preview['hero_images'] = [];
        $preview['galleries'] = [];

        return $preview;
    }

    /**
     * @return array{left: string, center: string}
     */
    private function footerMeta(): array
    {
        $user = Auth::user();
        $account = $user instanceof User ? $user->currentAccount() : null;

        $userName = trim((string) ($user?->name ?? ''));
        $accountName = trim((string) ($account instanceof Account
            ? ($account->commercial_name ?? $account->name ?? '')
            : ''));

        $leftParts = array_values(array_filter([$userName, $accountName], fn (string $part) => $part !== ''));

        return [
            'left' => $leftParts !== [] ? implode(' — ', $leftParts) : '—',
            'center' => 'Simple-Travel',
        ];
    }

    /**
     * @param  array{left: string, center: string}  $footerMeta
     */
    private function applyFooter(Dompdf $dompdf, array $footerMeta): void
    {
        $canvas = $dompdf->getCanvas();
        $fontMetrics = $dompdf->getFontMetrics();
        $font = $fontMetrics->getFont('DejaVu Sans', 'normal');
        $size = 8;
        $color = [0.45, 0.45, 0.45];
        $width = $canvas->get_width();
        $height = $canvas->get_height();
        $y = $height - 28;
        $lineY = $y - 10;

        $canvas->line(40, $lineY, $width - 40, $lineY, $color, 0.5);

        $left = (string) ($footerMeta['left'] ?? '—');
        $center = (string) ($footerMeta['center'] ?? 'Simple-Travel');

        $canvas->page_text(40, $y, $left, $font, $size, $color);

        $centerWidth = $fontMetrics->getTextWidth($center, $font, $size);
        $canvas->page_text(max(40, ($width - $centerWidth) / 2), $y, $center, $font, $size, $color);

        $canvas->page_text(
            $width - 130,
            $y,
            __('account.package_offers.agency_preview_pdf_pages'),
            $font,
            $size,
            $color,
        );
    }

    /**
     * @param  array<string, mixed>  $preview
     * @return array<string, mixed>
     */
    private function embedPreviewMedia(array $preview): array
    {
        if (isset($preview['hero_images']) && is_array($preview['hero_images'])) {
            foreach ($preview['hero_images'] as $index => $image) {
                if (is_array($image) && isset($image['url'])) {
                    $preview['hero_images'][$index] = array_merge(
                        $image,
                        $this->embedImageForPdf((string) $image['url'], maxWidth: 520, maxHeight: 300),
                    );
                }
            }
        }

        if (isset($preview['galleries']) && is_array($preview['galleries'])) {
            foreach ($preview['galleries'] as $galleryIndex => $gallery) {
                if (! is_array($gallery) || ! isset($gallery['images']) || ! is_array($gallery['images'])) {
                    continue;
                }

                foreach ($gallery['images'] as $imageIndex => $image) {
                    if (is_array($image) && isset($image['url'])) {
                        $preview['galleries'][$galleryIndex]['images'][$imageIndex] = array_merge(
                            $image,
                            $this->embedImageForPdf((string) $image['url'], maxWidth: 165, maxHeight: 120),
                        );
                    }
                }
            }
        }

        return $preview;
    }

    /**
     * @return array{url: string, width: int|null, height: int|null}
     */
    private function embedImageForPdf(string $url, int $maxWidth, int $maxHeight): array
    {
        $empty = ['url' => '', 'width' => null, 'height' => null];

        $path = $this->resolveLocalMediaPath($url);
        if ($path !== null && is_readable($path)) {
            $contents = @file_get_contents($path);
            if ($contents !== false && $contents !== '') {
                return $this->buildEmbeddedImagePayload(
                    $contents,
                    $this->mimeTypeForPath($path),
                    $maxWidth,
                    $maxHeight,
                );
            }
        }

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            $contents = @file_get_contents($url);
            if ($contents !== false && $contents !== '') {
                $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH) ?: '', PATHINFO_EXTENSION));

                return $this->buildEmbeddedImagePayload(
                    $contents,
                    $this->mimeTypeForExtension($extension),
                    $maxWidth,
                    $maxHeight,
                );
            }
        }

        return $empty;
    }

    /**
     * @return array{url: string, width: int|null, height: int|null}
     */
    private function buildEmbeddedImagePayload(
        string $contents,
        string $mimeType,
        int $maxWidth,
        int $maxHeight,
    ): array {
        $dataUri = 'data:'.$mimeType.';base64,'.base64_encode($contents);
        $dimensions = $this->scaleImageDimensions($contents, $maxWidth, $maxHeight);

        return [
            'url' => $dataUri,
            'width' => $dimensions['width'],
            'height' => $dimensions['height'],
        ];
    }

    /**
     * @return array{width: int|null, height: int|null}
     */
    private function scaleImageDimensions(string $contents, int $maxWidth, int $maxHeight): array
    {
        $info = @getimagesizefromstring($contents);
        if ($info === false) {
            return ['width' => $maxWidth, 'height' => null];
        }

        $naturalWidth = max(1, (int) ($info[0] ?? 1));
        $naturalHeight = max(1, (int) ($info[1] ?? 1));
        $scale = min($maxWidth / $naturalWidth, $maxHeight / $naturalHeight, 1.0);

        return [
            'width' => max(1, (int) round($naturalWidth * $scale)),
            'height' => max(1, (int) round($naturalHeight * $scale)),
        ];
    }

    private function resolveLocalMediaPath(string $url): ?string
    {
        $url = trim($url);
        if ($url === '') {
            return null;
        }

        if (is_file($url)) {
            return $url;
        }

        $path = parse_url($url, PHP_URL_PATH);
        if (! is_string($path) || $path === '') {
            return null;
        }

        if (str_starts_with($path, '/storage/')) {
            $relative = ltrim(substr($path, strlen('/storage/')), '/');
            $candidate = storage_path('app/public'.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relative));
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        $publicCandidate = public_path(ltrim($path, '/\\'));
        if (is_file($publicCandidate)) {
            return $publicCandidate;
        }

        return null;
    }

    private function mimeTypeForPath(string $path): string
    {
        if (function_exists('mime_content_type')) {
            $detected = @mime_content_type($path);
            if (is_string($detected) && $detected !== '') {
                return $detected;
            }
        }

        return $this->mimeTypeForExtension(strtolower(pathinfo($path, PATHINFO_EXTENSION)));
    }

    private function mimeTypeForExtension(string $extension): string
    {
        return match ($extension) {
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            default => 'image/jpeg',
        };
    }
}
