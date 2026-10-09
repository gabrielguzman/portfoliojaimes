<?php

namespace App\Filament\Resources\ArtSeries\Pages;

use App\Filament\Resources\ArtSeries\ArtSeriesResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditArtSeries extends EditRecord
{
    protected static string $resource = ArtSeriesResource::class;

    protected function getHeaderActions(): array
    {
        return [Action::make('public')->label('Ver en el sitio')->url(fn () => route('series.show', $this->getRecord()->slug))->openUrlInNewTab()->visible(fn () => $this->getRecord()->published)];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()->success()->title('Contenido guardado')->body($this->getRecord()->published ? 'Ya está publicado en el sitio.' : 'Quedó como borrador. Activá Publicado cuando esté listo.');
    }
}
