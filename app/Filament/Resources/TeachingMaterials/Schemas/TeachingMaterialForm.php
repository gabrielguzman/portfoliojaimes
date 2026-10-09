<?php

namespace App\Filament\Resources\TeachingMaterials\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class TeachingMaterialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Presentación')->schema([
                TextInput::make('title')->label('Título')->required()->maxLength(200)->live(onBlur: true)->afterStateUpdated(fn (Set $set, ?string $state, string $operation) => $operation === 'create' ? $set('slug', Str::slug($state ?? '')) : null),
                TextInput::make('slug')->label('Dirección')->required()->maxLength(200)->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')->unique(ignoreRecord: true),
                Textarea::make('description')->label('Descripción')->required()->rows(6)->columnSpanFull(),  TextInput::make('audience')->label('Destinatarios')->maxLength(255)->helperText('Por ejemplo: nivel secundario, docentes o público general.'), FileUpload::make('file')->label('Material en PDF')->disk('local')->directory('materials')->visibility('private')->acceptedFileTypes(['application/pdf'])->maxSize(10240)->required()->helperText('Hasta 10 MB. La descarga estará disponible solo al publicar.')->columnSpanFull(),
            ])->columns(2)->columnSpanFull(),
            Section::make('Publicación')->schema([Toggle::make('published')->label('Publicado')->default(false)->helperText('Desactivado: permanece como borrador y no aparece en el sitio.'), TextInput::make('sort_order')->label('Orden')->numeric()->minValue(0)->default(0)->required()])->columns(2)->columnSpanFull(),
        ]);
    }
}
