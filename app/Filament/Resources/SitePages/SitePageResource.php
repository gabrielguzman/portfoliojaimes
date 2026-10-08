<?php

namespace App\Filament\Resources\SitePages;

use App\Filament\Resources\SitePages\Pages\CreateSitePage;
use App\Filament\Resources\SitePages\Pages\EditSitePage;
use App\Filament\Resources\SitePages\Pages\ListSitePages;
use App\Filament\Resources\SitePages\Schemas\SitePageForm;
use App\Filament\Resources\SitePages\Tables\SitePagesTable;
use App\Models\SitePage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SitePageResource extends Resource
{
    protected static ?string $model = SitePage::class;
    protected static ?string $modelLabel = 'página';
    protected static ?string $pluralModelLabel = 'Páginas y textos';
    protected static ?string $recordTitleAttribute = 'title';
    protected static string|\UnitEnum|null $navigationGroup = 'Sitio';
    protected static ?string $navigationLabel = 'Páginas y textos';
    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    public static function getCreateAuthorizationResponse(): \Illuminate\Auth\Access\Response { return \Illuminate\Auth\Access\Response::deny('Las páginas del sitio ya están definidas.'); }
    public static function getDeleteAuthorizationResponse(\Illuminate\Database\Eloquent\Model $record): \Illuminate\Auth\Access\Response { return \Illuminate\Auth\Access\Response::deny('Esta página forma parte del sitio.'); }
    public static function form(Schema $schema): Schema
    {
        return SitePageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SitePagesTable::configure($table);
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
            'index' => ListSitePages::route('/'),
            'create' => CreateSitePage::route('/create'),
            'edit' => EditSitePage::route('/{record}/edit'),
        ];
    }
}
