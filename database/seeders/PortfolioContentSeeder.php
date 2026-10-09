<?php

namespace Database\Seeders;

use App\Models\ArtSeries;
use App\Models\PortfolioEvent;
use App\Models\Project;
use App\Models\TeachingMaterial;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class PortfolioContentSeeder extends Seeder
{
    public function run(): void
    {
        foreach (glob(database_path('demo-assets/*.svg')) as $asset) {
            $path = 'demo/'.basename($asset);
            if (! Storage::disk('public')->exists($path)) {
                Storage::disk('public')->put($path, file_get_contents($asset));
            }
        }
        $examples = [
            ['paisaje-interior', 'Paisaje interior', 'art', 'Pintura', 'territorios', 'Acrílico sobre papel', '42 × 59 cm', "Un paisaje puede empezar mucho antes de reconocer un lugar. Esta obra reúne capas de color, bordes que se encuentran y pequeñas zonas de silencio. Los tonos tierra dialogan con un verde profundo; una forma circular introduce una pausa en el recorrido.\n\nEl trabajo propone mirar el territorio como una experiencia sensible: una memoria fragmentada que se reconstruye con cada observación."],
            ['entre-dos-planos', 'Entre dos planos', 'art', 'Técnica mixta', 'silencios', 'Collage y dibujo sobre papel', '30 × 40 cm', "Dos planos se acercan sin terminar de coincidir. El recorte y la línea construyen un espacio donde cada borde parece continuar fuera de la imagen.\n\nLa composición nace de una búsqueda sobre equilibrio y desplazamiento. Las formas se ordenan con precisión, pero conservan una tensión que invita a demorarse en los intervalos."],
            ['memoria-del-hilo', 'Memoria del hilo', 'art', 'Arte textil', 'tramas', 'Investigación de tramas y fibras', '50 × 70 cm', "Una trama guarda el tiempo de los gestos que la construyen. Repeticiones, cruces y diferencias mínimas organizan esta investigación sobre la relación entre superficie y memoria.\n\nEl ritmo visual se interrumpe en algunos puntos para dejar aparecer lo irregular: aquello que vuelve singular a una estructura compartida."],
            ['linea-en-transito', 'Línea en tránsito', 'art', 'Dibujo', 'gestos', 'Grafito y tinta sobre papel', '21 × 29,7 cm', "El dibujo se desarrolla como un recorrido sin destino fijo. Una línea avanza, cambia de dirección y vuelve sobre sí misma; en ese movimiento encuentra una forma provisional.\n\nLa obra explora el gesto como una manera de pensar. El espacio vacío acompaña los trazos y permite que cada encuentro mantenga su intensidad."],
            ['aula-laboratorio-color', 'El aula como laboratorio de color', 'teaching', 'Pintura', 'territorios', 'Exploración de color y collage', '', "Una propuesta para descubrir que el color nunca se percibe de forma aislada. A partir de recortes y pequeñas composiciones, el grupo explora relaciones de contraste, temperatura y proporción.\n\nEl recorrido comienza con una observación compartida, continúa con una búsqueda individual y termina con una conversación sobre las decisiones. Importa probar, comparar y encontrar palabras para explicar lo que cambia."],
            ['cartografias-compartidas', 'Cartografías compartidas', 'teaching', 'Dibujo', 'gestos', 'Dibujo, bitácora y composición colectiva', '', "Un recorrido de observación del entorno cercano que transforma detalles cotidianos en un mapa colectivo. Líneas, texturas y palabras registran modos diferentes de habitar un mismo espacio.\n\nLa propuesta combina una bitácora personal con un montaje grupal. Cada participante aporta una mirada y el conjunto permite descubrir relaciones que no aparecían en los registros individuales."],
        ];
        $projects = [];
        foreach ($examples as $index => [$slug,$title,$section,$category,$asset,$technique,$dimensions,$description]) {
            $project = Project::withTrashed()->where('slug', $slug)->first();
            if (! $project) {
                $project = Project::create(['slug' => $slug, 'title' => $title, 'section' => $section, 'category' => $category, 'year' => 2026, 'technique' => $technique, 'dimensions' => $dimensions ?: null, 'description' => $description."\n\nContenido de ejemplo para mostrar el portfolio; no documenta una obra o experiencia real.", 'cover' => 'demo/'.$asset.'.svg', 'published' => true, 'featured' => $index === 0, 'sort_order' => 10 + $index]);
                $project->images()->create(['path' => 'demo/'.$asset.'.svg', 'alt' => 'Composición de ejemplo: '.$title, 'caption' => $title.' · Estudio visual de ejemplo', 'sort_order' => 0]);
            }
            $projects[$slug] = $project;
        }
        foreach ([
            ['territorios-de-la-mirada', 'Territorios de la mirada', 'territorios', ['paisaje-interior', 'entre-dos-planos', 'linea-en-transito'], "Esta colección reúne búsquedas sobre el paisaje, el espacio y las formas de mirar. No se trata de describir un lugar, sino de explorar cómo una imagen puede guardar una sensación.\n\nColor, recorte y línea proponen distintos recorridos. Las obras comparten una atención a los bordes y a los intervalos: zonas donde algo termina, comienza o permanece abierto."],
            ['la-forma-del-vinculo', 'La forma del vínculo', 'tramas', ['memoria-del-hilo', 'entre-dos-planos'], "Una superficie puede pensarse como una red de relaciones. Esta serie parte de la repetición y el encuentro para observar lo que sucede entre una parte y el conjunto.\n\nLas tramas y los planos construyen ritmos que admiten diferencias. En lugar de buscar una estructura cerrada, la colección propone una forma de convivencia entre lo regular y lo inesperado."],
        ] as $index => [$slug,$title,$asset,$members,$description]) {
            $series = ArtSeries::firstOrCreate(['slug' => $slug], ['title' => $title, 'description' => $description."\n\nColección de ejemplo para visualizar el sitio.", 'cover' => 'demo/'.$asset.'.svg', 'published' => true, 'sort_order' => $index]);
            if ($series->wasRecentlyCreated) {
                $series->projects()->attach(array_map(fn ($key) => $projects[$key]->id, $members));
            }
        }
        $today = today('America/Argentina/Catamarca');
        foreach ([
            ['muestra-territorios', 'Territorios: formas de mirar', 'exhibition', 14, 35, 'Sala de arte · espacio de ejemplo', 'territorios', "Una muestra que reúne exploraciones de color, dibujo y composición. El recorrido propone detenerse en las relaciones entre imagen, memoria y paisaje.\n\nLa visita puede realizarse de manera libre. En la apertura se propone una conversación sobre los procesos de las obras y las preguntas que sostienen la colección."],
            ['taller-color-en-relacion', 'Color en relación', 'workshop', 21, null, 'Aula taller · espacio de ejemplo', 'silencios', "Un encuentro para experimentar con papeles, paletas y contrastes. No requiere experiencia previa: la propuesta parte de observar cómo cambia un mismo color según su entorno.\n\nSe trabajará con materiales simples y una puesta en común final. La información de inscripción y los horarios se completarán cuando exista una actividad real."],
            ['encuentro-tramas', 'Tramas: una imagen entre todos', 'activity', -28, -28, 'Espacio de encuentro · ejemplo', 'tramas', "Una propuesta colectiva centrada en la repetición, la diferencia y los acuerdos. Cada participante construye un módulo y el grupo ensaya distintas formas de reunirlos.\n\nEl archivo presenta el enfoque del encuentro y abre preguntas sobre la participación, el registro y las decisiones compartidas."],
            ['muestra-linea-y-pausa', 'Línea y pausa', 'exhibition', -75, -55, 'Sala de proyectos · espacio de ejemplo', 'gestos', "Un recorrido dedicado al dibujo como registro de un pensamiento en movimiento. Las composiciones dialogan a través de gestos, vacíos y cambios de escala.\n\nEsta entrada permite visualizar cómo se conserva la memoria de una exposición una vez que termina."],
        ] as [$slug,$title,$kind,$start,$end,$location,$asset,$description]) {
            PortfolioEvent::firstOrCreate(['slug' => $slug], ['title' => $title, 'kind' => $kind, 'starts_on' => $today->copy()->addDays($start)->toDateString(), 'ends_on' => $end === null ? null : $today->copy()->addDays($end)->toDateString(), 'location' => $location, 'description' => $description."\n\nActividad ficticia de ejemplo. Las fechas y los espacios no corresponden a una convocatoria real.", 'cover' => 'demo/'.$asset.'.svg', 'published' => true]);
        }
        foreach ([
            ['laboratorio-color', 'Laboratorio de color', 'Nivel secundario y talleres abiertos', 'Una secuencia de dos encuentros para explorar cómo se transforma un color según los tonos que lo rodean. Incluye materiales, tiempos, preguntas para la puesta en común y alternativas de participación.'],
            ['bitacora-mirada', 'Bitácora de la mirada', 'Docentes y estudiantes de artes visuales', 'Cinco consignas breves para observar formas, texturas, espacios y detalles del entorno. Un punto de partida para construir un registro personal y abrir nuevas investigaciones visuales.'],
            ['tramas-compartidas', 'Tramas compartidas', 'Grupos de secundaria y espacios comunitarios', 'Una propuesta de composición colectiva a partir de módulos individuales. Incluye una secuencia de trabajo, criterios de acompañamiento y preguntas sobre los acuerdos que construyen una imagen común.'],
        ] as $index => [$slug,$title,$audience,$description]) {
            $path = 'materials/examples/'.$slug.'.pdf';
            if (! Storage::disk('local')->exists($path)) {
                Storage::disk('local')->put($path, file_get_contents(database_path('demo-assets/materials/'.$slug.'.pdf')));
            }
            TeachingMaterial::firstOrCreate(['slug' => $slug], ['title' => $title, 'description' => $description."\n\nMaterial de ejemplo, listo para revisar y adaptar a cada grupo.", 'audience' => $audience, 'file' => $path, 'published' => true, 'sort_order' => $index]);
        }
    }
}
