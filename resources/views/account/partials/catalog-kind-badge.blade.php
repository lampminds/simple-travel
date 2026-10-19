@if (($catalog ?? null) instanceof \App\Models\OperatorServiceCatalog && $catalog->isDirectService())
    <div class="mt-1">
        <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle">
            {{ __('account.catalog_kind.service') }}
        </span>
    </div>
@endif
