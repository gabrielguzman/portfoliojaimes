<?php

namespace App\Filament\Widgets;

use App\Models\Profile;
use App\Models\Project;
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

        return ['pending' => $pending];
    }
}
