<?php

namespace App\Filament\Resources\ArtSeries\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ArtSeriesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Presentación')->schema([
                TextInput::make('title')->label('Título')->required()->maxLength(200)->live(onBlur: true)->afterStateUpdated(fn (Set $set, ?string $state, string $operation) => $operation === 'create' ? $set('slug', Str::slug($state ?? '')) : null),
                TextInput::make('slug')->label('Dirección')->required()->maxLength(200)->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')->unique(ignoreRecord: true),
                Textarea::make('description')->label('Descripción')->required()->rows(6)->columnSpanFull(), FileUpload::make('cover')->label('Imagen de presentación')->disk('public')->directory('editorial')->visibility('public')->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])->maxSize(10240), Select::make('projects')->label('Proyectos de la serie')->relationship('projects', 'title', fn ($query) => $query->where('section', 'art'))->multiple()->searchable()->preload()->helperText('Solo se mostrarán los proyectos publicados. Su orden sigue el de Obra.')->columnSpanFull(),
            ])->columns(2)->columnSpanFull(),
            Section::make('Publicación')->schema([Toggle::make('published')->label('Publicado')->default(false)->helperText('Desactivado: permanece como borrador y no aparece en el sitio.'), TextInput::make('sort_order')->label('Orden')->numeric()->minValue(0)->default(0)->required()])->columns(2)->columnSpanFull(),
        ]);
    }
}
