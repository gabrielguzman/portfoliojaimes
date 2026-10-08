<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Resources\SitePages\SitePageResource;
use App\Models\Profile;
use App\Models\Project;
use App\Models\SitePage;
use Filament\Widgets\Widget;

class ContentGuide extends Widget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.content-guide';

    protected function getViewData(): array
    {
        $profile = Profile::first();
        $pending = [];
        if (! $profile?->portrait) {
            $pending[] = 'Agregar una foto a Sobre mí.';
        }
        if (! $profile?->cv) {
            $pending[] = 'Subir el CV en PDF, si querés compartirlo.';
        }
        if (! $profile?->email && ! $profile?->instagram) {
            $pending[] = 'Completar al menos un canal de contacto.';
        }
        if (! $profile?->bio || str_contains($profile->bio, 'La biografía se completará')) {
            $pending[] = 'Reemplazar la biografía de ejemplo por la presentación de Romina.';
        }
        if (! $profile?->teaching_statement) {
            $pending[] = 'Personalizar el enfoque docente en Perfil y contacto.';
        }
        if (empty($profile?->trajectory)) {
            $pending[] = 'Agregar formación y experiencias reales a la trayectoria.';
        }
        if (Project::where('cover', 'like', 'demo/%')->exists()) {
            $pending[] = 'Reemplazar las obras de demostración por proyectos reales.';
        }

        $home = SitePage::where('key', 'home')->first();

        return ['pending' => $pending, 'tasks' => [
            [ProjectResource::getUrl('create'), 'Crear proyecto', 'Empezá una obra o una experiencia docente. Podés guardarla como borrador.'],
            [ProjectResource::getUrl('index'), 'Subir imágenes', 'Elegí un proyecto y usá su acción Imágenes para agregar y ordenar la galería.'],
            [$home ? SitePageResource::getUrl('edit', ['record' => $home]) : SitePageResource::getUrl('index'), 'Editar inicio', 'Actualizá la presentación, los textos y la selección destacada.'],
        ]];
    }
}
