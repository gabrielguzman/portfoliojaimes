<?php
namespace App\Filament\Resources\SitePages\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\{EditAction,Action};
class SitePagesTable {
 public static function configure(Table $table): Table {
  return $table->columns([
   TextColumn::make('title')->label('Página'),TextColumn::make('path')->label('Dirección'),
   TextColumn::make('updated_at')->label('Última actualización')->dateTime('d/m/Y H:i'),
  ])->recordActions([EditAction::make(),Action::make('open')->label('Ver página')->url(fn($record)=>$record->path)->openUrlInNewTab()])->paginated(false);
 }
}
