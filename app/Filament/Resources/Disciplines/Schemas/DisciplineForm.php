<?php
namespace App\Filament\Resources\Disciplines\Schemas;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
class DisciplineForm {
 public static function configure(Schema $schema): Schema {
  return $schema->components([TextInput::make('name')->label('Nombre de la disciplina')->required()->maxLength(100)->unique(ignoreRecord:true)->helperText('Al renombrarla también se actualizan los proyectos que la utilizan.')]);
 }
}
