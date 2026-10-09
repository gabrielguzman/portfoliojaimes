<?php

namespace App\Filament\Resources\ArtSeries\Pages;

use App\Filament\Resources\ArtSeries\ArtSeriesResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListArtSeries extends ListRecords
{
    protected static string $resource = ArtSeriesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
