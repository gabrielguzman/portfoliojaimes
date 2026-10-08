<?php
namespace App\Filament\Resources\Profiles\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
class ProfilesTable {
    public static function configure(Table $table): Table {
        return $table->columns([TextColumn::make('name')->label('Nombre'),TextColumn::make('email')->label('Contacto')])->recordActions([EditAction::make()]);
    }
}
