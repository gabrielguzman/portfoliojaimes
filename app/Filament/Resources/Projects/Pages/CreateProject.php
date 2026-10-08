<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\Concerns\UploadsGallery;
use App\Filament\Resources\Projects\ProjectResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateProject extends CreateRecord
{
    use UploadsGallery;

    protected ?bool $hasDatabaseTransactions = true;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->takeGalleryUploads($data);
    }

    protected function afterCreate(): void
    {
        $this->appendGalleryUploads();
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }

    protected static string $resource = ProjectResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()->success()->title('Proyecto creado')->body($this->getRecord()->published ? 'Ya está publicado. Podés seguir completando su galería.' : 'Quedó guardado como borrador. Completá la galería y revisá la vista previa antes de publicar.');
    }
}
