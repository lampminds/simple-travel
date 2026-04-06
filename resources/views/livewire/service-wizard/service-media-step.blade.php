<div>
    @if ($flashMessage)
        <div class="alert alert-success py-2 small" role="status">{{ $flashMessage }}</div>
    @endif

    <div class="row g-4">
        <div class="col-md-6">
            <h5 class="h6 mb-2">{{ __('wizard.media_main_heading') }}</h5>
            <p class="text-muted small mb-3">{{ __('wizard.media_main_help') }}</p>

            @if ($mainMedia)
                <div class="mb-3">
                    <img
                        src="{{ $mainMedia->getAvailableUrl([\App\Models\Service::MEDIA_CONVERSION_SMALL]) }}"
                        alt=""
                        class="img-fluid rounded border"
                        style="max-height: 200px; object-fit: contain;"
                    >
                </div>
                <button
                    type="button"
                    class="btn btn-outline-danger btn-sm mb-3"
                    wire:click="removeMain"
                    wire:confirm="{{ __('wizard.media_main_remove_confirm') }}"
                    wire:loading.attr="disabled"
                >
                    {{ __('wizard.media_main_remove') }}
                </button>
            @endif

            <div class="mb-2">
                <input type="file" class="form-control form-control-sm" accept="image/*" wire:model="mainPhoto">
                @error('mainPhoto')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div wire:loading wire:target="mainPhoto" class="text-muted small mb-2">{{ __('wizard.media_uploading') }}</div>
            <button
                type="button"
                class="btn btn-primary btn-sm"
                wire:click="saveMain"
                wire:loading.attr="disabled"
                @if (! $mainPhoto) disabled @endif
            >
                {{ __('wizard.media_main_upload') }}
            </button>
        </div>

        <div class="col-md-6">
            <h5 class="h6 mb-2">{{ __('wizard.media_gallery_heading') }}</h5>
            <p class="text-muted small mb-3">{{ __('wizard.media_gallery_help') }}</p>

            @if ($galleryMedia->isNotEmpty())
                <ul class="list-group list-group-flush border rounded mb-3">
                    @foreach ($galleryMedia as $item)
                        <li class="list-group-item d-flex align-items-center gap-2 py-2">
                            <img
                                src="{{ $item->getAvailableUrl([\App\Models\Service::MEDIA_CONVERSION_THUMBNAIL]) }}"
                                alt=""
                                class="rounded border flex-shrink-0"
                                style="width: 48px; height: 48px; object-fit: cover;"
                            >
                            <span class="small text-truncate flex-grow-1">{{ $item->name ?: $item->file_name }}</span>
                            <button
                                type="button"
                                class="btn btn-outline-danger btn-sm"
                                wire:click="removeGalleryMedia({{ (int) $item->id }})"
                                wire:confirm="{{ __('wizard.media_gallery_remove_confirm') }}"
                                wire:loading.attr="disabled"
                            >
                                {{ __('wizard.media_gallery_remove') }}
                            </button>
                        </li>
                    @endforeach
                </ul>
            @endif

            <div class="mb-2">
                <input
                    type="file"
                    class="form-control form-control-sm"
                    accept="image/*"
                    wire:model="galleryPhotos"
                    multiple
                >
                @error('galleryPhotos')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
                @error('galleryPhotos.*')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div wire:loading wire:target="galleryPhotos" class="text-muted small mb-2">{{ __('wizard.media_uploading') }}</div>
            <button
                type="button"
                class="btn btn-primary btn-sm"
                wire:click="addGallery"
                wire:loading.attr="disabled"
            >
                {{ __('wizard.media_gallery_upload') }}
            </button>
        </div>
    </div>
</div>
