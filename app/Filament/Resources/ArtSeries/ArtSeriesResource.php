<?php

namespace App\Filament\Resources\ArtSeries;

use App\Filament\Resources\ArtSeries\Pages\CreateArtSeries;
use App\Filament\Resources\ArtSeries\Pages\EditArtSeries;
use App\Filament\Resources\ArtSeries\Pages\ListArtSeries;
use App\Filament\Resources\ArtSeries\Schemas\ArtSeriesForm;
use App\Filament\Resources\ArtSeries\Tables\ArtSeriesTable;
use App\Models\ArtSeries;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ArtSeriesResource extends Resource
{
    protected static ?string $model = ArtSeries::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Series y colecciones';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $modelLabel = 'serie';

    protected static ?string $pluralModelLabel = 'Series y colecciones';

    protected static string|\UnitEnum|null $navigationGroup = 'Contenido';

    public static function form(Schema $schema): Schema
    {
        return ArtSeriesForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ArtSeriesTable::configure($table);
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
            'index' => ListArtSeries::route('/'),
            'create' => CreateArtSeries::route('/create'),
            'edit' => EditArtSeries::route('/{record}/edit'),
        ];
    }
}
