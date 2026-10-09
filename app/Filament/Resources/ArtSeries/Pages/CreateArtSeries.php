<?php

namespace App\Filament\Resources\ArtSeries\Pages;

use App\Filament\Resources\ArtSeries\ArtSeriesResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateArtSeries extends CreateRecord
{
    protected static string $resource = ArtSeriesResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()->success()->title('Contenido guardado')->body($this->getRecord()->published ? 'Ya está publicado en el sitio.' : 'Quedó como borrador. Activá Publicado cuando esté listo.');
    }
}
