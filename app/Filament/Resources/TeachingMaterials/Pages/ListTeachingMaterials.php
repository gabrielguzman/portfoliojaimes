<?php

namespace App\Filament\Resources\TeachingMaterials\Pages;

use App\Filament\Resources\TeachingMaterials\TeachingMaterialResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTeachingMaterials extends ListRecords
{
    protected static string $resource = TeachingMaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
