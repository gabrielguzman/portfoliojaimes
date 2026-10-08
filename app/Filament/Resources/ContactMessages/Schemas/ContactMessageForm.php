<?php

namespace App\Filament\Resources\ContactMessages\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Consulta recibida')->schema([
                TextInput::make('name')->label('Nombre')->disabled()->dehydrated(false),
                TextInput::make('email')->label('Correo')->disabled()->dehydrated(false),
                TextInput::make('subject')->label('Asunto')->disabled()->dehydrated(false)->columnSpanFull(),
                Textarea::make('message')->label('Mensaje')->rows(10)->disabled()->dehydrated(false)->columnSpanFull(),
                Select::make('status')->label('Seguimiento')->options(['new' => 'Nueva', 'read' => 'Leída', 'answered' => 'Respondida'])->required(),
            ])->columns(2)->columnSpanFull(),
        ]);
    }
}
