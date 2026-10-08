<?php
namespace Database\Seeders;
use App\Models\{Profile,Project};
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder {
    public function run(): void {
        Profile::firstOrCreate(['id'=>1],[
            'name'=>'Romina Elizabeth Jaimes',
            'intro'=>'Artes visuales y docencia. Un espacio para compartir obras, procesos y experiencias de aprendizaje.',
            'bio'=>"Este espacio reunirá la trayectoria, la mirada artística y las experiencias docentes de Romina. La biografía se completará con su presentación personal.\n\nLas obras que se muestran en esta primera versión son composiciones de demostración y no pertenecen a su producción artística.",
        ]);
        if (!app()->environment('local','testing')) return;
        \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory('demo');
        foreach (glob(database_path('demo-assets/*.svg')) as $asset) { \Illuminate\Support\Facades\Storage::disk('public')->put('demo/'.basename($asset), file_get_contents($asset)); }
        foreach ([['Territorios sensibles','territorios','Pintura','Estudio de color y paisaje'],['La forma del silencio','silencios','Técnica mixta','Composición geométrica'],['Tramas de lo cotidiano','tramas','Arte textil','Estudio digital de tramas'],['Un gesto, muchas miradas','gestos','Dibujo','Estudio digital de líneas']] as $i=>$data) {
            $p=Project::firstOrCreate(['slug'=>$data[1]],[
                'title'=>$data[0], 'description'=>"OBRA DE DEMOSTRACIÓN.\n\nEsta composición digital se incluye únicamente para mostrar el diseño del portfolio. No es una obra de Romina Elizabeth Jaimes. Desde el panel se puede reemplazar por un proyecto real, editar su descripción y agregar imágenes del proceso.",
                'category'=>$data[2], 'year'=>2026,'technique'=>$data[3],'cover'=>'demo/'.$data[1].'.svg','published'=>true,'featured'=>$i===0,'sort_order'=>$i,
            ]);
            $p->images()->firstOrCreate(['path'=>'demo/'.$data[1].'.svg'],['alt'=>'Composición abstracta de demostración: '.$data[0], 'caption'=>'Imagen de demostración · Reemplazar por una obra original','sort_order'=>0]);
        }
    }
}
