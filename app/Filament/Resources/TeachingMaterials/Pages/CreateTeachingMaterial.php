<?php

namespace App\Filament\Resources\TeachingMaterials\Pages;

use App\Filament\Resources\TeachingMaterials\TeachingMaterialResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateTeachingMaterial extends CreateRecord
{
    protected static string $resource = TeachingMaterialResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()->success()->title('Contenido guardado')->body($this->getRecord()->published ? 'Ya está publicado en el sitio.' : 'Quedó como borrador. Activá Publicado cuando esté listo.');
    }
}
