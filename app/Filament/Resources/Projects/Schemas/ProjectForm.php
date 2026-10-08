<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Models\Discipline;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('1. Sobre el proyecto')->description('Presentá la obra y completá su ficha.')->schema([
                TextInput::make('title')->label('Título')->required()->maxLength(200)->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state, string $operation) => $operation === 'create' ? $set('slug', Str::slug($state ?? '')) : null),
                TextInput::make('slug')->label('Dirección del proyecto')->required()->maxLength(200)->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')->unique(ignoreRecord: true),
                Textarea::make('description')->label('Descripción')->required()->rows(6)->columnSpanFull(),
                Select::make('section')->label('Sección del sitio')->options(['art' => 'Obra', 'teaching' => 'Docencia'])->default('art')->required()->helperText('Elegí dónde aparecerá el proyecto. La disciplina se configura por separado.'),
                Select::make('category')->label('Disciplina')->options(fn () => Discipline::options())->searchable()->required(),
                TextInput::make('year')->label('Año')->numeric()->minValue(1900)->maxValue(2100)->default(date('Y'))->required(),
                TextInput::make('technique')->label('Técnica')->maxLength(200), TextInput::make('dimensions')->label('Medidas')->maxLength(200),
            ])->columns(2)->columnSpanFull(),
            Section::make('2. Imágenes')->id('project-images')->extraAttributes(['style' => 'scroll-margin-top: 90px'])->description('Elegí la portada, subí las imágenes y completá sus pies. JPG, PNG o WebP; hasta 10 MB por imagen.')->schema([
                FileUpload::make('cover')->label('Portada')->disk('public')->directory('projects')->visibility('public')->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])->maxSize(10240)->required(),
                FileUpload::make('bulk_images')->label('Agregar varias imágenes')->helperText('Hasta 12 por carga. Guardá el proyecto para incorporarlas; después completá sus descripciones en la galería. Los originales se conservan.')->multiple()->maxFiles(12)->reorderable()->disk('public')->directory('projects')->visibility('public')->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])->maxSize(10240)->default([]),
                Repeater::make('images')->label('Galería')->relationship()->orderColumn('sort_order')->reorderable()->schema([
                    FileUpload::make('path')->label('Imagen')->disk('public')->directory('projects')->visibility('public')->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])->maxSize(10240)->required(),
                    TextInput::make('alt')->label('Descripción accesible')->helperText('Describí brevemente qué se ve en la imagen.')->required()->maxLength(255),
                    TextInput::make('caption')->label('Pie de imagen')->maxLength(255),
                ])->addActionLabel('Agregar imagen')->defaultItems(0)->collapsible(),
            ])->columnSpanFull(),
            Section::make('3. Publicación')->description('Guardá como borrador para revisar, o publicá cuando esté listo.')->schema([
                Toggle::make('published')->label('Publicado')->helperText('Desactivado: el proyecto queda como borrador.')->default(false),
                Toggle::make('featured')->label('Destacado en el inicio')->default(false),
                TextInput::make('sort_order')->label('Orden')->numeric()->minValue(0)->default(0)->required(),
            ])->columns(3)->columnSpanFull(),
        ]);
    }
}
