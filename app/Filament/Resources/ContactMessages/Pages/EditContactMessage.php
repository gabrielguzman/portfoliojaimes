<?php

namespace App\Filament\Resources\ContactMessages\Pages;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditContactMessage extends EditRecord
{
    protected static string $resource = ContactMessageResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return ['status' => $data['status']];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reply')->label('Responder por correo')->url(fn (): string => 'mailto:'.$this->getRecord()->email.'?subject='.rawurlencode('Re: '.$this->getRecord()->subject)),
            DeleteAction::make()->label('Eliminar consulta'),
        ];
    }
}
