<?php

namespace App\Filament\Resources\PortfolioEvents\Tables;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PortfolioEventTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->label('Título')->searchable(), TextColumn::make('starts_on')->label('Fecha')->date('d/m/Y')->sortable(), TextColumn::make('location')->label('Lugar'), IconColumn::make('published')->label('Publicado')->boolean(),
        ])->defaultSort('starts_on', 'desc')->filters([TernaryFilter::make('published')->label('Publicado')])->recordActions([EditAction::make(), Action::make('public')->label('Ver en el sitio')->url(fn ($record) => route('agenda').'#actividad-'.$record->slug)->openUrlInNewTab()->visible(fn ($record) => $record->published)]);
    }
}
