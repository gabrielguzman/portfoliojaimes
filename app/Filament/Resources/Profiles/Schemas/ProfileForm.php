<?php

namespace App\Filament\Resources\Profiles\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Identidad y presentación')->schema([
                TextInput::make('name')->label('Nombre público')->required()->maxLength(200),
                TextInput::make('role')->label('Profesión o presentación corta')->required()->maxLength(120),
                TextInput::make('brand_subtitle')->label('Subtítulo debajo del nombre')->required()->maxLength(80),
                TextInput::make('location')->label('Localidad')->maxLength(200),
                Textarea::make('intro')->label('Presentación breve')->rows(3)->required()->maxLength(1000)->columnSpanFull(),
                Textarea::make('bio')->label('Biografía y enfoque docente')->rows(8)->required()->maxLength(15000)->columnSpanFull(),
            ])->columns(2)->columnSpanFull(),
            Section::make('Docencia y trayectoria')->description('Cargá únicamente datos reales. La trayectoria aparece en Sobre mí cuando agregás entradas; podés ordenarlas arrastrando.')->schema([
                Textarea::make('teaching_statement')->label('Enfoque docente personal')->helperText('Opcional. Reemplaza la presentación general del enfoque en Docencia.')->rows(5)->maxLength(5000)->columnSpanFull(),
                Repeater::make('trajectory')->label('Formación, experiencia y exposiciones')->schema([
                    TextInput::make('period')->label('Año o período')->required()->maxLength(80),
                    Select::make('category')->label('Tipo')->options(['Formación' => 'Formación', 'Docencia' => 'Docencia', 'Exposición' => 'Exposición', 'Proyecto' => 'Proyecto', 'Otro' => 'Otro'])->required(),
                    TextInput::make('title')->label('Título, institución o actividad')->required()->maxLength(200)->columnSpanFull(),
                    Textarea::make('description')->label('Detalle')->rows(3)->maxLength(2000)->columnSpanFull(),
                ])->columns(2)->defaultItems(0)->maxItems(50)->addActionLabel('Agregar una experiencia')->itemLabel(fn (array $state): ?string => $state['title'] ?? null)->collapsible()->columnSpanFull(),
            ])->columnSpanFull(),
            Section::make('Foto y currículum')->description('La foto reemplaza la composición gráfica de Sobre mí. El CV queda disponible para descargar públicamente.')->schema([
                FileUpload::make('portrait')->label('Foto de perfil')->disk('public')->directory('profile')->visibility('public')->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])->maxSize(10240),
                TextInput::make('portrait_alt')->label('Descripción accesible de la foto')->maxLength(255),
                FileUpload::make('cv')->label('Currículum en PDF')->disk('public')->directory('curriculum')->visibility('public')->acceptedFileTypes(['application/pdf'])->maxSize(10240)->downloadable(),
            ])->columnSpanFull(),
            Section::make('Contacto y pie de página')->schema([
                TextInput::make('email')->label('Correo de contacto público')->email()->maxLength(255),
                TextInput::make('instagram')->label('Enlace a Instagram')->url()->startsWith('https://')->maxLength(255),
                TextInput::make('footer_text')->label('Frase del pie de página')->required()->maxLength(200)->columnSpanFull(),
            ])->columns(2)->columnSpanFull(),
        ]);
    }
}
