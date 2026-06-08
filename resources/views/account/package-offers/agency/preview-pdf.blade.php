<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('account.package_offers.agency_preview_modal_title') }} — {{ $preview['title'] ?? '' }}</title>
    <style>
        * { box-sizing: border-box; }
        @page {
            margin: 24px 24px 52px 24px;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            line-height: 1.45;
            color: #1f2937;
            margin: 0;
            padding: 0;
        }
        .pdf-doc-title {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #6b7280;
            margin-bottom: 4px;
        }
        .pdf-doc-heading {
            font-size: 18px;
            font-weight: bold;
            margin: 0 0 18px;
            color: #111827;
        }
        .preview-label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #6b7280;
            margin-bottom: 2px;
        }
        .preview-hero-row { width: 100%; margin: 10px 0; }
        .preview-hero-item {
            display: block;
            width: 100%;
            margin-bottom: 8px;
        }
        .preview-hero-img {
            border: 1px solid #e5e7eb;
            display: block;
        }
        .preview-hero-label {
            font-size: 9px;
            color: #6b7280;
            margin-top: 4px;
        }
        .preview-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            font-size: 10px;
        }
        .preview-table th,
        .preview-table td {
            border: 1px solid #e5e7eb;
            padding: 5px 6px;
            vertical-align: top;
            text-align: left;
        }
        .preview-table th {
            background: #f3f4f6;
            font-size: 9px;
            text-transform: uppercase;
            color: #6b7280;
        }
        .preview-muted { color: #6b7280; }
        .preview-conditions-list {
            margin: 0;
            padding: 0;
        }
        .preview-condition-line {
            margin-bottom: 4px;
            line-height: 1.35;
        }
        .preview-section-block {
            margin-top: 16px;
            page-break-inside: avoid;
        }
        .preview-section-title {
            font-size: 13px;
            font-weight: bold;
            margin: 0 0 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #d1d5db;
            color: #111827;
        }
        .preview-gallery-grid { width: 100%; }
        .preview-gallery-img {
            display: inline-block;
            vertical-align: top;
            margin: 0 1% 8px 0;
            border: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="pdf-doc-title">{{ __('account.package_offers.agency_preview_modal_title') }}</div>
    <h1 class="pdf-doc-heading">{{ $preview['title'] ?? '' }}</h1>

    @include('account.package-offers.agency.partials.preview-body', [
        'preview' => $preview,
        'forPdf' => true,
    ])
</body>
</html>
