<?php

namespace App\Filament\Resources\PortfolioEvents\Pages;

use App\Filament\Resources\PortfolioEvents\PortfolioEventResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPortfolioEvent extends EditRecord
{
    protected static string $resource = PortfolioEventResource::class;

    protected function getHeaderActions(): array
    {
        return [Action::make('public')->label('Ver en el sitio')->url(fn () => route('agenda').'#actividad-'.$this->getRecord()->slug)->openUrlInNewTab()->visible(fn () => $this->getRecord()->published)];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()->success()->title('Contenido guardado')->body($this->getRecord()->published ? 'Ya está publicado en el sitio.' : 'Quedó como borrador. Activá Publicado cuando esté listo.');
    }
}
