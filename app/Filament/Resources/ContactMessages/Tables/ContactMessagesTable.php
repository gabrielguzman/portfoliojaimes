<?php

namespace App\Filament\Resources\ContactMessages\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ContactMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('Nombre')->searchable(),
            TextColumn::make('subject')->label('Asunto')->searchable()->limit(60),
            TextColumn::make('email')->label('Correo')->searchable(),
            TextColumn::make('status')->label('Estado')->badge()->formatStateUsing(fn (string $state): string => match ($state) {
                'new' => 'Nueva','read' => 'Leída','answered' => 'Respondida',default => $state
            })->color(fn (string $state): string => $state === 'new' ? 'warning' : 'success'),
            TextColumn::make('created_at')->label('Recibida')->dateTime('d/m/Y H:i')->sortable(),
        ])->defaultSort('created_at', 'desc')->filters([
            SelectFilter::make('status')->label('Estado')->options(['new' => 'Nueva', 'read' => 'Leída', 'answered' => 'Respondida']),
  ])->recordActions([EditAction::make()->label('Leer consulta')])->emptyStateHeading('Todavía no hay consultas')->emptyStateDescription('Los mensajes del formulario de Contacto aparecerán aquí.');
    }
}
