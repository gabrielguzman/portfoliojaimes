<?php

namespace App\Filament\Resources\TeachingMaterials\Pages;

use App\Filament\Resources\TeachingMaterials\TeachingMaterialResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditTeachingMaterial extends EditRecord
{
    protected static string $resource = TeachingMaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [Action::make('public')->label('Ver en el sitio')->url(fn () => route('materials').'#material-'.$this->getRecord()->slug)->openUrlInNewTab()->visible(fn () => $this->getRecord()->published)];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()->success()->title('Contenido guardado')->body($this->getRecord()->published ? 'Ya está publicado en el sitio.' : 'Quedó como borrador. Activá Publicado cuando esté listo.');
    }
}
