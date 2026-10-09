<?php

namespace App\Filament\Resources\PortfolioEvents;

use App\Filament\Resources\PortfolioEvents\Pages\CreatePortfolioEvent;
use App\Filament\Resources\PortfolioEvents\Pages\EditPortfolioEvent;
use App\Filament\Resources\PortfolioEvents\Pages\ListPortfolioEvents;
use App\Filament\Resources\PortfolioEvents\Schemas\PortfolioEventForm;
use App\Filament\Resources\PortfolioEvents\Tables\PortfolioEventsTable;
use App\Models\PortfolioEvent;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PortfolioEventResource extends Resource
{
    protected static ?string $model = PortfolioEvent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Exposiciones y agenda';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $modelLabel = 'actividad';

    protected static ?string $pluralModelLabel = 'Exposiciones y agenda';

    protected static string|\UnitEnum|null $navigationGroup = 'Contenido';

    public static function form(Schema $schema): Schema
    {
        return PortfolioEventForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PortfolioEventsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPortfolioEvents::route('/'),
            'create' => CreatePortfolioEvent::route('/create'),
            'edit' => EditPortfolioEvent::route('/{record}/edit'),
        ];
    }
}
