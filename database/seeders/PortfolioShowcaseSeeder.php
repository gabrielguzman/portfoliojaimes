<?php

namespace Database\Seeders;

use App\Models\ArtSeries;
use App\Models\PortfolioEvent;
use App\Models\Profile;
use App\Models\Project;
use App\Models\SitePage;
use App\Models\TeachingMaterial;
use App\Services\PortfolioImages;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PortfolioShowcaseSeeder extends Seeder
{
    public function run(): void
    {
        $home = SitePage::where('key', 'home')->firstOrFail();
        if ($home->content['showcase_seeded'] ?? false) {
            return;
        }
        $this->call(PortfolioContentSeeder::class);
        foreach (glob(database_path('demo-assets/showcase/*.png')) as $asset) {
            $path = 'showcase/'.basename($asset);
            if (! Storage::disk('public')->exists($path)) {
                Storage::disk('public')->put($path, file_get_contents($asset));
            }
        }
        $projectImages = ['paisaje-interior' => 'painting', 'entre-dos-planos' => 'collage', 'memoria-del-hilo' => 'textile', 'linea-en-transito' => 'drawing', 'aula-laboratorio-color' => 'studio', 'cartografias-compartidas' => 'drawing'];
        foreach ($projectImages as $slug => $image) {
            $project = Project::where('slug', $slug)->first();
            if (! $project || ! Str::startsWith($project->cover ?? '', 'demo/')) {
                continue;
            }
            $path = 'showcase/'.$image.'.png';
            if ($slug === 'paisaje-interior') {
                $project->description = str_replace('una forma circular introduce una pausa en el recorrido.', 'las capas claras abren una pausa en el recorrido.', $project->description);
            }
            $project->update(['description' => str_replace("\n\nContenido de ejemplo para mostrar el portfolio; no documenta una obra o experiencia real.", '', $project->description), 'cover' => $path, 'featured' => $slug === 'paisaje-interior', 'sort_order' => array_search($slug, array_keys($projectImages))]);
            foreach ($project->images as $item) {
                if (Str::startsWith($item->path, 'demo/')) {
                    $item->update(['path' => $path, 'alt' => $project->title.' · '.$project->technique, 'caption' => $project->title.' · '.$project->year]);
                }
            }
            if ($project->section === 'art') {
                $project->images()->firstOrCreate(['path' => 'showcase/studio.png'], ['alt' => 'Mesa de trabajo con papeles, estudios de color y herramientas', 'caption' => 'Del cuaderno a la obra: materiales y búsquedas en el taller.', 'sort_order' => 1]);
            }
        }
        foreach (['territorios', 'silencios', 'tramas', 'gestos'] as $slug) {
            $project = Project::where('slug', $slug)->where('cover', 'demo/'.$slug.'.svg')->first();
            if ($project && str_contains($project->description, 'OBRA DE DEMOSTRACIÓN')) {
                $project->update(['published' => false, 'featured' => false]);
            }
        }
        foreach (['territorios-de-la-mirada' => 'painting', 'la-forma-del-vinculo' => 'textile'] as $slug => $image) {
            $series = ArtSeries::where('slug', $slug)->first();
            if ($series && Str::startsWith($series->cover ?? '', 'demo/')) {
                $series->update(['cover' => app(PortfolioImages::class)->optimize('showcase/'.$image.'.png', 1400) ?? 'showcase/'.$image.'.png', 'description' => str_replace("\n\nColección de ejemplo para visualizar el sitio.", '', $series->description)]);
            }
        }
        $events = [
            'muestra-territorios' => ['Sala Umbral', 'Pasaje del Molino 184', 'painting', "Una muestra que reúne exploraciones de color, dibujo y composición. El recorrido propone detenerse en las relaciones entre imagen, memoria y paisaje.\n\nApertura el jueves 22 de octubre a las 19 h, con conversación sobre los procesos de las obras. Visitas de martes a sábado, de 17 a 20 h. Entrada libre."],
            'taller-color-en-relacion' => ['Taller La Mesa', 'Calle de los Álamos 326', 'studio', "Un encuentro para experimentar con papeles, paletas y contrastes. La propuesta parte de observar cómo cambia un mismo color según su entorno y construir composiciones propias.\n\nJueves 29 de octubre, de 16 a 18 h. Para jóvenes y personas adultas, sin experiencia previa. Materiales incluidos; grupo de hasta 12 participantes. Consultas e inscripción mediante el formulario de contacto."],
            'encuentro-tramas' => ['Casa de Artes del Patio', 'Calle del Patio 72', 'textile', "Una imagen construida entre muchas manos. Cada participante desarrolló un módulo a partir de papel y fibras; el grupo ensayó distintos acuerdos para reunirlos en una composición común.\n\nEl encuentro del 10 de septiembre cerró con un montaje colectivo y una conversación sobre los ritmos, las diferencias y las decisiones compartidas."],
            'muestra-linea-y-pausa' => ['Sala Umbral', 'Pasaje del Molino 184', 'drawing', "Un recorrido dedicado al dibujo como registro de un pensamiento en movimiento. Las composiciones dialogaron a través de gestos, vacíos y cambios de escala.\n\nLa muestra reunió estudios sobre papel y una selección de cuadernos de trabajo. Durante el cierre se realizó una visita conversada sobre observación y proceso."],
        ];
        foreach ($events as $slug => [$location,$address,$image,$description]) {
            $event = PortfolioEvent::where('slug', $slug)->first();
            if ($event && str_contains($event->description, 'Actividad ficticia de ejemplo.')) {
                $event->update(['location' => $location, 'address' => $address, 'cover' => app(PortfolioImages::class)->optimize('showcase/'.$image.'.png', 1400) ?? 'showcase/'.$image.'.png', 'description' => $description]);
            }
        }
        foreach (TeachingMaterial::whereIn('slug', ['laboratorio-color', 'bitacora-mirada', 'tramas-compartidas'])->get() as $material) {
            $material->update(['description' => str_replace("\n\nMaterial de ejemplo, listo para revisar y adaptar a cada grupo.", '', $material->description)]);
        }
        $profile = Profile::first();
        if (! $profile) {
            $profile = Profile::create(['name' => 'Romina Elizabeth Jaimes', 'intro' => '', 'bio' => '']);
        }
        if (! $profile->bio || str_contains($profile->bio, 'La biografía se completará')) {
            $profile->update([
                'role' => 'Artista visual y docente', 'brand_subtitle' => 'ARTE & DOCENCIA', 'location' => 'Catamarca, Argentina',
                'intro' => 'Trabajo con el color, la línea y las tramas para explorar las relaciones entre paisaje, memoria y vida cotidiana. La enseñanza forma parte de esa búsqueda: crear también es aprender a mirar con otros.',
                'bio' => "Soy artista visual y profesora de artes visuales. Mi práctica se mueve entre la pintura, el dibujo y la exploración textil. Me interesan los materiales por lo que permiten construir, pero también por las preguntas que aparecen al trabajar con ellos.\n\nMuchas de mis obras comienzan en un cuaderno: una forma observada durante un recorrido, una relación de colores o una línea que todavía no encuentra su lugar. El taller es el espacio donde esos registros se transforman, se superponen y vuelven a abrirse.\n\nEn la docencia propongo experiencias que dan lugar a la observación, la experimentación y el intercambio. Acompaño procesos en los que cada persona puede tomar decisiones, reconocer su propia mirada y aprender de las producciones del grupo.",
                'portrait' => 'showcase/studio.png', 'portrait_alt' => 'El taller: cuadernos, materiales y estudios de color sobre una mesa',
                'teaching_statement' => 'Entiendo el aula como un lugar de investigación compartida. Las consignas abren posibilidades: observar con atención, probar materiales, revisar una decisión y conversar sobre lo que aparece. Acompañar ese recorrido importa tanto como la producción final.',
                'trajectory' => [
                    ['period' => '2025 — 2026', 'category' => 'INVESTIGACIÓN', 'title' => 'Paisaje, memoria y superficie', 'description' => 'Desarrollo de las series Territorios de la mirada y La forma del vínculo, con cruces entre pintura, dibujo y trama.'],
                    ['period' => '2023 — 2026', 'category' => 'DOCENCIA', 'title' => 'El aula como espacio de exploración', 'description' => 'Diseño de secuencias sobre color, observación y composición colectiva para jóvenes y personas adultas.'],
                    ['period' => '2021 — 2024', 'category' => 'PRÁCTICA ARTÍSTICA', 'title' => 'Cuadernos y procesos de taller', 'description' => 'Construcción de una bitácora visual como punto de partida para obras sobre papel y búsquedas con fibras.'],
                ],
            ]);
        }
        $profile->update(['footer_text' => 'Muestra ficticia del portfolio · Biografía, obras, imágenes y agenda simuladas.']);
        $contact = SitePage::where('key', 'contact')->first();
        if ($contact) {
            $contact->update(['content' => array_replace($contact->content ?? [], ['email_unavailable' => 'Las consultas se reciben mediante el formulario.', 'instagram_unavailable' => 'Novedades de talleres y exposiciones en Agenda.'])]);
        }
        $home->update(['content' => array_replace($home->content ?? [], ['showcase_seeded' => true, 'featured_count' => 4])]);
    }
}
