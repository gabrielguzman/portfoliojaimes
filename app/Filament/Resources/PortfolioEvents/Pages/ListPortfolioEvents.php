<?php

namespace App\Filament\Resources\PortfolioEvents\Pages;

use App\Filament\Resources\PortfolioEvents\PortfolioEventResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPortfolioEvents extends ListRecords
{
    protected static string $resource = PortfolioEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
