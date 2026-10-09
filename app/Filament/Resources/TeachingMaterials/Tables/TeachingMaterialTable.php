<?php

namespace App\Filament\Resources\TeachingMaterials\Tables;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class TeachingMaterialTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->label('Título')->searchable(),  IconColumn::make('published')->label('Publicado')->boolean(),
        ])->defaultSort('sort_order')->reorderable('sort_order')->filters([TernaryFilter::make('published')->label('Publicado')])->recordActions([EditAction::make(), Action::make('public')->label('Ver en el sitio')->url(fn ($record) => route('materials').'#material-'.$record->slug)->openUrlInNewTab()->visible(fn ($record) => $record->published)]);
    }
}
