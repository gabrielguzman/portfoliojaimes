<?php

namespace App\Filament\Resources\SitePages\Schemas;

use App\Models\SitePage;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SitePageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Contenido de la página')->description('Los cambios se publican al guardar. Cada bloque corresponde a una parte visible del sitio.')->schema(function (?SitePage $record) {
                if (! $record) {
                    return [];
                }
                $groups = [];
                foreach (config('site_content.'.$record->key.'.fields', []) as $key => $label) {
                    $group = match (true) {
                        str_starts_with($key, 'selection_'),$key === 'featured_count',$key === 'focus_label',$key === 'all_works_cta' => 'Selección de proyectos',
                        str_starts_with($key, 'docencia_'),str_starts_with($key, 'about_') => 'Accesos a otras secciones',
                        str_starts_with($key, 'empty_') => 'Cuando no hay proyectos',
                        str_starts_with($key, 'presentation_'),str_starts_with($key, 'trajectory_'),$key === 'cv_cta' => 'Presentación personal',
                        str_starts_with($key, 'approach_'),str_starts_with($key, 'pillar_'),str_starts_with($key, 'proposals_') => 'Enfoque y propuestas docentes',
                        str_starts_with($key, 'process_') => 'Procesos artísticos',
                        str_starts_with($key, 'form_') => 'Formulario de contacto',
                        str_contains($key, '_path_') => 'Accesos a otras secciones',
                        str_starts_with($key, 'contact_'),str_starts_with($key, 'continue_'),str_starts_with($key, 'greeting'),str_starts_with($key, 'email_'),str_starts_with($key, 'instagram_'),$key === 'signoff' => 'Contacto y cierre',
                        default => 'Menú y encabezado',
                    };
                    if ($key === 'featured_count') {
                        $field = TextInput::make('content.'.$key)->label($label)->numeric()->integer()->minValue(1)->maxValue(8)->required();
                    } elseif (str_contains($key, 'description') || str_contains($key, 'invitation')) {
                        $field = Textarea::make('content.'.$key)->label($label)->rows(3)->maxLength(1000)->required()->columnSpanFull();
                    } else {
                        $field = TextInput::make('content.'.$key)->label($label)->required()->maxLength($key === 'menu_label' ? 30 : 160);
                    }
                    $groups[$group][] = $field;
                }
                $sections = [];
                foreach ($groups as $title => $fields) {
                    $sections[] = Section::make($title)->schema($fields)->columns(2)->collapsible()->columnSpanFull();
                }

                return $sections;
            })->columnSpanFull(),
            Section::make('Buscadores')->schema([Textarea::make('seo_description')->label('Descripción para buscadores')->helperText('Opcional. Resumí la página en una o dos frases.')->rows(3)->maxLength(300)])->collapsible()->columnSpanFull(),
        ]);
    }
}
