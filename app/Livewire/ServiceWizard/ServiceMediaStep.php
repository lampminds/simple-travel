<?php

namespace App\Livewire\ServiceWizard;

use App\Models\Service;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class ServiceMediaStep extends Component
{
    use WithFileUploads;

    public int $serviceId;

    public int $serviceTypeId;

    /** @var mixed */
    public $mainPhoto = null;

    /** @var array<int, mixed> */
    public array $galleryPhotos = [];

    public ?string $flashMessage = null;

    public function mount(int $serviceId, int $serviceTypeId): void
    {
        $this->serviceId = $serviceId;
        $this->serviceTypeId = $serviceTypeId;
    }

    public function updatedMainPhoto(): void
    {
        $this->resetValidation('mainPhoto');
    }

    public function updatedGalleryPhotos(): void
    {
        $this->resetValidation('galleryPhotos');
    }

    public function saveMain(): void
    {
        $this->validate([
            'mainPhoto' => ['required', 'image', 'max:'.Service::MEDIA_MAX_FILE_SIZE_KB],
        ]);

        $service = $this->authorizedService();

        $service
            ->addMedia($this->mainPhoto)
            ->toMediaCollection(Service::MEDIA_COLLECTION_MAIN);

        $this->mainPhoto = null;
        $this->flashMessage = __('wizard.media_main_saved');
    }

    public function removeMain(): void
    {
        $this->authorizedService()->clearMediaCollection(Service::MEDIA_COLLECTION_MAIN);
        $this->flashMessage = __('wizard.media_main_removed');
    }

    public function addGallery(): void
    {
        $this->validate([
            'galleryPhotos' => ['required', 'array', 'min:1'],
            'galleryPhotos.*' => ['image', 'max:'.Service::MEDIA_MAX_FILE_SIZE_KB],
        ]);

        $service = $this->authorizedService();

        foreach ($this->galleryPhotos as $file) {
            $service
                ->addMedia($file)
                ->toMediaCollection(Service::MEDIA_COLLECTION_GALLERY);
        }

        $this->galleryPhotos = [];
        $this->flashMessage = __('wizard.media_gallery_saved');
    }

    public function removeGalleryMedia(int $mediaId): void
    {
        $service = $this->authorizedService();
        $media = $service
            ->media()
            ->where('collection_name', Service::MEDIA_COLLECTION_GALLERY)
            ->whereKey($mediaId)
            ->first();

        if ($media !== null) {
            $media->delete();
            $this->flashMessage = __('wizard.media_gallery_removed');
        }
    }

    protected function authorizedService(): Service
    {
        $accountId = Auth::user()?->currentAccountId();
        abort_unless($accountId, 403);

        return Service::query()
            ->where('account_id', $accountId)
            ->where('service_type_id', $this->serviceTypeId)
            ->findOrFail($this->serviceId);
    }

    public function render(): View
    {
        $service = $this->authorizedService();
        $service->load('media');

        return view('livewire.service-wizard.service-media-step', [
            'mainMedia' => $service->getFirstMedia(Service::MEDIA_COLLECTION_MAIN),
            'galleryMedia' => $service->getMedia(Service::MEDIA_COLLECTION_GALLERY),
        ]);
    }
}
