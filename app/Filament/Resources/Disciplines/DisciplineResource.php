<?php

namespace App\Filament\Resources\Disciplines;

use App\Filament\Resources\Disciplines\Pages\CreateDiscipline;
use App\Filament\Resources\Disciplines\Pages\EditDiscipline;
use App\Filament\Resources\Disciplines\Pages\ListDisciplines;
use App\Filament\Resources\Disciplines\Schemas\DisciplineForm;
use App\Filament\Resources\Disciplines\Tables\DisciplinesTable;
use App\Models\Discipline;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DisciplineResource extends Resource
{
    protected static ?string $model = Discipline::class;
    protected static ?string $modelLabel = 'disciplina';
    protected static ?string $pluralModelLabel = 'Disciplinas';
    protected static ?string $recordTitleAttribute = 'name';
    protected static string|\UnitEnum|null $navigationGroup = 'Contenido';
    protected static ?string $navigationLabel = 'Disciplinas';
    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    public static function getDeleteAuthorizationResponse(\Illuminate\Database\Eloquent\Model $record): \Illuminate\Auth\Access\Response { return \App\Models\Project::withTrashed()->where('category',$record->name)->exists() ? \Illuminate\Auth\Access\Response::deny('La disciplina tiene proyectos asociados.') : \Illuminate\Auth\Access\Response::allow(); }
    public static function form(Schema $schema): Schema
    {
        return DisciplineForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DisciplinesTable::configure($table);
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
            'index' => ListDisciplines::route('/'),
            'create' => CreateDiscipline::route('/create'),
            'edit' => EditDiscipline::route('/{record}/edit'),
        ];
    }
}
