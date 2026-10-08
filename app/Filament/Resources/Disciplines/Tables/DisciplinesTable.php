<?php
namespace App\Filament\Resources\Disciplines\Tables;
use App\Models\Project;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\{EditAction,DeleteAction};
class DisciplinesTable {
 public static function configure(Table $table): Table {
  return $table->columns([
   TextColumn::make('name')->label('Disciplina')->searchable()->sortable(),
   TextColumn::make('usage')->label('Proyectos')->state(fn($record)=>Project::withTrashed()->where('category',$record->name)->count()),
  ])->recordActions([EditAction::make(),DeleteAction::make()]);
 }
}
