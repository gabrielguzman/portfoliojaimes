<?php

namespace App\Filament\Resources\SitePages\Pages;

use App\Filament\Resources\SitePages\SitePageResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditSitePage extends EditRecord
{
    protected static string $resource = SitePageResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['content'] = array_replace(config('site_content.'.$this->getRecord()->key.'.defaults', []), $data['content'] ?? []);

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('open')->label('Ver página')->url(fn () => $this->getRecord()->path)->openUrlInNewTab(),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()->success()->title('Página actualizada')->body('Los textos ya están publicados. Usá Ver página para revisar el resultado.');
    }
}
