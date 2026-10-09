<?php

namespace Tests\Feature;

use App\Filament\Resources\ArtSeries\Pages\CreateArtSeries;
use App\Filament\Resources\PortfolioEvents\Pages\CreatePortfolioEvent;
use App\Filament\Resources\TeachingMaterials\Pages\CreateTeachingMaterial;
use App\Models\ArtSeries;
use App\Models\PortfolioEvent;
use App\Models\Project;
use App\Models\TeachingMaterial;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Livewire\Livewire;
use Tests\TestCase;

class EditorialContentTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): void
    {
        $user = User::factory()->create();
        $user->is_admin = true;
        $user->save();
        $this->actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    private function project(string $slug, bool $published = true, string $section = 'art'): Project
    {
        return Project::create(['title' => $slug, 'slug' => $slug, 'description' => 'Una obra', 'year' => 2026, 'category' => 'Pintura', 'published' => $published, 'section' => $section]);
    }

    public function test_series_only_show_published_art_and_preserve_collection_navigation(): void
    {
        $series = ArtSeries::factory()->create(['published' => true, 'slug' => 'color']);
        $first = $this->project('primera');
        $second = $this->project('segunda');
        $draft = $this->project('borrador', false);
        $teaching = $this->project('taller', true, 'teaching');
        $deleted = $this->project('eliminada');
        $deleted->delete();
        $series->projects()->attach([$first->id, $second->id, $draft->id, $teaching->id, $deleted->id]);
        ArtSeries::factory()->create(['slug' => 'privada']);
        $this->get('/series')->assertInertia(fn (Assert $page) => $page->component('Editorial')->has('items', 1)->where('items.0.count', 2));
        $this->get('/series/color')->assertInertia(fn (Assert $page) => $page->component('Series')->has('projects', 2)->where('projects.0.slug', 'primera'));
        $this->get('/proyectos/primera?serie=color')->assertInertia(fn (Assert $page) => $page->where('collectionUrl', route('series.show', 'color'))->where('next.url', route('projects.show', 'segunda').'?serie=color')->where('total', 2));
        $this->get('/series/privada')->assertNotFound();
        $this->get('/proyectos/primera?serie=privada')->assertInertia(fn (Assert $page) => $page->where('collectionUrl', route('artwork')));
        $this->get('/proyectos/primera?serie[]=color')->assertOk();
    }

    public function test_agenda_uses_end_date_to_separate_upcoming_activities_and_archive(): void
    {
        $this->travelTo(now()->setDate(2026, 10, 8)->startOfDay());
        PortfolioEvent::factory()->create(['published' => true, 'slug' => 'en-curso', 'starts_on' => '2026-10-01', 'ends_on' => '2026-10-10']);
        PortfolioEvent::factory()->create(['published' => true, 'slug' => 'hoy', 'starts_on' => '2026-10-08']);
        PortfolioEvent::factory()->create(['published' => true, 'slug' => 'pasada', 'starts_on' => '2026-09-01']);
        PortfolioEvent::factory()->create(['slug' => 'privada']);
        $this->get('/agenda')->assertInertia(fn (Assert $page) => $page->has('items', 3)->where('items.0.past', true)->where('items.1.past', false)->where('items.2.past', false));
    }

    public function test_material_downloads_exclude_drafts_and_missing_files(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('materials/guia.pdf', '%PDF-1.4');
        TeachingMaterial::factory()->create(['published' => true, 'slug' => 'guia']);
        TeachingMaterial::factory()->create(['slug' => 'privado']);
        TeachingMaterial::factory()->create(['published' => true, 'slug' => 'sin-archivo', 'file' => 'materials/missing.pdf']);
        $this->get('/docencia/materiales')->assertInertia(fn (Assert $page) => $page->has('items', 1)->where('items.0.slug', 'guia')->missing('items.0.file'));
        $this->get('/docencia/materiales/guia/descargar')->assertDownload('guia.pdf')->assertHeader('Content-Type', 'application/pdf')->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->get('/docencia/materiales/privado/descargar')->assertNotFound();
        $this->get('/docencia/materiales/sin-archivo/descargar')->assertNotFound();
    }

    public function test_admin_can_create_a_series_and_attach_existing_projects(): void
    {
        $this->admin();
        $project = $this->project('obra');
        Livewire::test(CreateArtSeries::class)->fillForm(['title' => 'Miradas', 'slug' => 'miradas', 'description' => 'Un recorrido curatorial', 'projects' => [$project->id], 'published' => true, 'sort_order' => 0])->call('create')->assertHasNoFormErrors();
        $series = ArtSeries::where('slug', 'miradas')->firstOrFail();
        $this->assertSame($project->id, $series->projects->sole()->id);
        $this->get('/series/miradas')->assertOk();
    }

    public function test_admin_rejects_event_end_before_start_and_can_publish_valid_dates(): void
    {
        $this->admin();
        $form = Livewire::test(CreatePortfolioEvent::class)->fillForm(['title' => 'Encuentro', 'slug' => 'encuentro', 'description' => 'Muestra colectiva', 'kind' => 'exhibition', 'starts_on' => '2026-12-10', 'ends_on' => '2026-12-01', 'location' => 'Sala de arte', 'published' => true]);
        $form->call('create')->assertHasFormErrors(['ends_on']);
        $this->assertDatabaseMissing('portfolio_events', ['slug' => 'encuentro']);
        $form->fillForm(['ends_on' => '2026-12-20'])->call('create')->assertHasNoFormErrors();
        $this->assertDatabaseHas('portfolio_events', ['slug' => 'encuentro', 'published' => true]);
    }

    public function test_admin_can_upload_pdf_and_rejects_other_file_types(): void
    {
        $this->admin();
        Storage::fake('local');
        Livewire::test(CreateTeachingMaterial::class)->fillForm(['title' => 'Guía', 'slug' => 'guia', 'description' => 'Exploración de color', 'audience' => 'Docentes', 'file' => UploadedFile::fake()->createWithContent('guia.pdf', "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\n%%EOF"), 'published' => true, 'sort_order' => 0])->call('create')->assertHasNoFormErrors();
        $material = TeachingMaterial::where('slug', 'guia')->firstOrFail();
        Storage::disk('local')->assertExists($material->file);
        $this->get('/docencia/materiales/guia/descargar')->assertDownload('guia.pdf');
        Livewire::test(CreateTeachingMaterial::class)->fillForm(['title' => 'Otro', 'slug' => 'otro', 'description' => 'Texto', 'file' => UploadedFile::fake()->createWithContent('texto.txt', 'No es un PDF'), 'published' => false, 'sort_order' => 0])->call('create')->assertHasFormErrors(['file']);
        $this->assertDatabaseMissing('teaching_materials', ['slug' => 'otro']);
    }

    public function test_new_admin_resources_require_an_admin(): void
    {
        foreach (['/admin/art-series', '/admin/portfolio-events', '/admin/teaching-materials'] as $path) {
            $this->get($path)->assertRedirect('/admin/login');
        }
        $this->actingAs(User::factory()->create());
        foreach (['/admin/art-series', '/admin/portfolio-events', '/admin/teaching-materials'] as $path) {
            $this->get($path)->assertForbidden();
        }
        $this->admin();
        foreach (['/admin/art-series', '/admin/portfolio-events', '/admin/teaching-materials'] as $path) {
            $this->get($path)->assertOk();
        }
    }
}
