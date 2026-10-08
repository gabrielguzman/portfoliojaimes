<?php

namespace App\Filament\Resources\Profiles;

use App\Filament\Resources\Profiles\Pages\CreateProfile;
use App\Filament\Resources\Profiles\Pages\EditProfile;
use App\Filament\Resources\Profiles\Pages\ListProfiles;
use App\Filament\Resources\Profiles\Schemas\ProfileForm;
use App\Filament\Resources\Profiles\Tables\ProfilesTable;
use App\Models\Profile;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProfileResource extends Resource
{
    protected static ?string $model = Profile::class;
    protected static string|\UnitEnum|null $navigationGroup = 'Sitio';
    protected static ?string $navigationLabel = 'Perfil y contacto';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'perfil';
    protected static ?string $pluralModelLabel = 'Mi perfil';
    protected static ?string $recordTitleAttribute = 'name';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    public static function getCreateAuthorizationResponse(): \Illuminate\Auth\Access\Response { return Profile::exists() ? \Illuminate\Auth\Access\Response::deny('Ya existe un perfil.') : \Illuminate\Auth\Access\Response::allow(); }
    public static function getDeleteAuthorizationResponse(\Illuminate\Database\Eloquent\Model $record): \Illuminate\Auth\Access\Response { return \Illuminate\Auth\Access\Response::deny('El perfil no se puede eliminar.'); }

    public static function form(Schema $schema): Schema
    {
        return ProfileForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProfilesTable::configure($table);
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
            'index' => ListProfiles::route('/'),
            'create' => CreateProfile::route('/create'),
            'edit' => EditProfile::route('/{record}/edit'),
        ];
    }
}
