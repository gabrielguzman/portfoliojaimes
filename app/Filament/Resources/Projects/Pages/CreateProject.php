<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProject extends CreateRecord
{
    use \App\Filament\Resources\Projects\Concerns\UploadsGallery;
    protected ?bool $hasDatabaseTransactions=true;
    protected function mutateFormDataBeforeCreate(array $data): array { return $this->takeGalleryUploads($data); }
    protected function afterCreate(): void { $this->appendGalleryUploads(); }
    protected function getRedirectUrl(): string { return static::getResource()::getUrl('edit',['record'=>$this->getRecord()]); }
    protected static string $resource = ProjectResource::class;
}
