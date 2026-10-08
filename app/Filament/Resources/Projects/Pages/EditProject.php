<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\Concerns\UploadsGallery;
use App\Filament\Resources\Projects\ProjectResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditProject extends EditRecord
{
    use UploadsGallery;

    protected ?bool $hasDatabaseTransactions = true;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->takeGalleryUploads($data);
    }

    protected function afterSave(): void
    {
        $this->appendGalleryUploads();
        $this->getRecord()->unsetRelation('images');
        $this->fillForm();
    }

    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')->label('Vista previa')->url(fn () => route('projects.preview', $this->getRecord()))->openUrlInNewTab()->visible(fn () => ! $this->getRecord()->trashed()),
            RestoreAction::make()->label('Recuperar borrador'),
            DeleteAction::make()->label('Enviar a papelera')->modalDescription('Podrás recuperarlo con sus imágenes desde el filtro Papelera.'),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()->success()->title('Proyecto guardado')->body($this->getRecord()->published ? 'Los cambios ya están publicados en el sitio.' : 'El proyecto sigue como borrador. Podés revisarlo con Vista previa.');
    }
}
