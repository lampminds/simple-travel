<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('account.service_offers.operator_preview_modal_title') }} — {{ $preview['title'] ?? '' }}</title>
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
        .preview-header {
            width: 100%;
            margin-bottom: 16px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 12px;
        }
        .preview-header-meta { margin-bottom: 8px; }
        .preview-badge {
            display: inline-block;
            background: #e0e7ff;
            color: #3730a3;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 10px;
            margin-right: 8px;
        }
        .preview-provider { color: #6b7280; }
        .preview-price-block { text-align: right; margin-top: 8px; }
        .preview-label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #6b7280;
            margin-bottom: 2px;
        }
        .preview-price {
            font-size: 20px;
            font-weight: bold;
            color: #2563eb;
        }
        .preview-price-hint {
            font-size: 10px;
            color: #6b7280;
            margin-top: 4px;
        }
        .preview-intro { margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #e5e7eb; }
        .preview-line { margin-bottom: 6px; }
        .preview-value { font-weight: 600; }
        .preview-locale { margin-top: 12px; padding-top: 10px; border-top: 1px solid #f3f4f6; }
        .preview-locale-title { font-size: 14px; font-weight: bold; margin-top: 4px; }
        .preview-locale-desc { color: #6b7280; margin: 6px 0 0; }
        .preview-card {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 14px;
        }
        .preview-price-list {
            border: 1px solid #e5e7eb;
            background: #f9fafb;
            padding: 10px;
            margin: 10px 0;
        }
        .preview-price-list-grid { width: 100%; }
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
        .preview-meta-grid { width: 100%; }
        .preview-meta-item {
            display: inline-block;
            width: 32%;
            vertical-align: top;
            margin-bottom: 10px;
            padding-right: 8px;
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
        .preview-subtitle { font-weight: 600; margin-bottom: 6px; }
        .preview-block { margin-bottom: 12px; }
        .preview-tags { margin: 0; color: #374151; }
        .preview-muted { color: #6b7280; }
        .preview-gallery-grid { width: 100%; }
        .preview-gallery-img {
            display: inline-block;
            vertical-align: top;
            margin: 0 1% 8px 0;
            border: 1px solid #e5e7eb;
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
    </style>
</head>
<body>
    <div class="pdf-doc-title">{{ __('account.service_offers.operator_preview_modal_title') }}</div>
    <h1 class="pdf-doc-heading">{{ $preview['title'] ?? '' }}</h1>

    @include('account.service-offers.operator.partials.preview-body', [
        'preview' => $preview,
        'forPdf' => true,
    ])
</body>
</html>
