<?php

namespace App\Filament\Resources\Projects\Tables;

use App\Filament\Resources\Projects\ProjectResource;
use App\Models\Discipline;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            ImageColumn::make('cover')->label('Portada')->disk('public'),
            TextColumn::make('title')->label('Proyecto')->searchable()->sortable(),
            TextColumn::make('section')->label('Sección')->formatStateUsing(fn ($state) => $state === 'teaching' ? 'Docencia' : 'Obra'),
            TextColumn::make('category')->label('Disciplina')->badge(),
            TextColumn::make('year')->label('Año')->sortable(),
            IconColumn::make('published')->label('Publicado')->boolean(),
            IconColumn::make('featured')->label('Destacado')->boolean(),
        ])->defaultSort('sort_order')->reorderable('sort_order')->filters([
            TrashedFilter::make()->label('Papelera')->placeholder('Proyectos activos')->trueLabel('Activos y en papelera')->falseLabel('Solo papelera'),
            TernaryFilter::make('published')->label('Publicado'),
            SelectFilter::make('section')->label('Sección')->options(['art' => 'Obra', 'teaching' => 'Docencia']),
            SelectFilter::make('category')->label('Disciplina')->options(fn () => Discipline::options()),
        ])->recordActions([
            Action::make('images')->label('Imágenes')->url(fn ($record) => ProjectResource::getUrl('edit', ['record' => $record]).'#project-images')->visible(fn ($record) => ! $record->trashed()),
            Action::make('preview')->label('Vista previa')->url(fn ($record) => route('projects.preview', $record))->openUrlInNewTab()->visible(fn ($record) => ! $record->trashed()),
            EditAction::make()->visible(fn ($record) => ! $record->trashed()),
            DeleteAction::make()->label('Enviar a papelera')->modalHeading('Enviar proyecto a la papelera')->modalDescription('Dejará de aparecer en el sitio. Podrás recuperarlo con sus imágenes.')->modalSubmitActionLabel('Enviar a papelera'),
            RestoreAction::make()->label('Recuperar borrador')->modalDescription('Se recuperará con sus imágenes como borrador. Revisalo antes de volver a publicarlo.'),
        ]);
    }
}
