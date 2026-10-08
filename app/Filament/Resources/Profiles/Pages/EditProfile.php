<?php

namespace App\Filament\Resources\Profiles\Pages;

use App\Filament\Resources\Profiles\ProfileResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditProfile extends EditRecord
{
    protected static string $resource = ProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('open')->label('Ver Sobre mí')->url('/sobre-mi')->openUrlInNewTab(),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()->success()->title('Perfil actualizado')->body('Los cambios ya están publicados en el sitio.');
    }
}
