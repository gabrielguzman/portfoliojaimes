<?php

namespace App\Filament\Resources\PortfolioEvents\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PortfolioEventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Presentación')->schema([
                TextInput::make('title')->label('Título')->required()->maxLength(200)->live(onBlur: true)->afterStateUpdated(fn (Set $set, ?string $state, string $operation) => $operation === 'create' ? $set('slug', Str::slug($state ?? '')) : null),
                TextInput::make('slug')->label('Dirección')->required()->maxLength(200)->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')->unique(ignoreRecord: true),
                Textarea::make('description')->label('Descripción')->required()->rows(6)->columnSpanFull(), FileUpload::make('cover')->label('Imagen de presentación')->disk('public')->directory('editorial')->visibility('public')->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])->maxSize(10240), Select::make('kind')->label('Tipo')->options(['exhibition' => 'Exposición', 'workshop' => 'Taller', 'activity' => 'Actividad'])->required()->default('exhibition'), DatePicker::make('starts_on')->label('Fecha de inicio')->required(), DatePicker::make('ends_on')->label('Fecha de cierre')->afterOrEqual('starts_on')->helperText('Opcional para actividades de un solo día.'), TextInput::make('location')->label('Lugar')->required()->maxLength(255), TextInput::make('address')->label('Dirección')->maxLength(255),
            ])->columns(2)->columnSpanFull(),
            Section::make('Publicación')->schema([Toggle::make('published')->label('Publicado')->default(false)->helperText('Desactivado: permanece como borrador y no aparece en el sitio.')])->columns(2)->columnSpanFull(),
        ]);
    }
}
