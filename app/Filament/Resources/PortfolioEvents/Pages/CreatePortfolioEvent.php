<?php

namespace App\Filament\Resources\PortfolioEvents\Pages;

use App\Filament\Resources\PortfolioEvents\PortfolioEventResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePortfolioEvent extends CreateRecord
{
    protected static string $resource = PortfolioEventResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()->success()->title('Contenido guardado')->body($this->getRecord()->published ? 'Ya está publicado en el sitio.' : 'Quedó como borrador. Activá Publicado cuando esté listo.');
    }
}
