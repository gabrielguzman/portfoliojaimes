<?php

namespace Tests\Feature;

use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Projects\Pages\EditProject;
use App\Models\Project;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Livewire\Livewire;
use Tests\TestCase;

class PortfolioTest extends TestCase
{
    use RefreshDatabase;

    private function project(bool $published = true): Project
    {
        return Project::create(['title' => 'Obra real', 'slug' => 'obra-real', 'description' => 'Descripción de prueba', 'category' => 'Pintura', 'year' => 2026, 'published' => $published]);
    }

    private function admin(): User
    {
        $u = User::factory()->create();
        $u->is_admin = true;
        $u->save();

        return $u;
    }

    public function test_only_published_projects_are_public(): void
    {
        $p = $this->project(false);
        $this->get('/')->assertOk()->assertInertia(fn (Assert $page) => $page->component('Portfolio')->has('projects', 0));
        $this->get('/proyectos/obra-real')->assertNotFound();
        $p->update(['published' => true]);
        $this->get('/')->assertInertia(fn (Assert $page) => $page->has('projects', 1));
        $this->get('/proyectos/obra-real')->assertOk()->assertInertia(fn (Assert $page) => $page->component('Project')->where('project.title', 'Obra real'));
    }

    public function test_admin_and_private_preview_are_protected(): void
    {
        $p = $this->project(false);
        $this->get('/admin/projects')->assertRedirect('/admin/login');
        $this->get('/vista-previa/'.$p->id)->assertForbidden();
        $ordinary = User::factory()->create();
        $this->actingAs($ordinary)->get('/admin/projects')->assertForbidden();
        $this->get('/vista-previa/'.$p->id)->assertForbidden();
        $this->actingAs($this->admin())->get('/admin/projects')->assertOk();
        $this->get('/vista-previa/'.$p->id)->assertOk()->assertInertia(fn (Assert $page) => $page->where('preview', true));
    }

    public function test_admin_can_upload_create_and_publish_a_project(): void
    {
        Storage::fake('public');
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $this->actingAs($this->admin());
        $component = Livewire::test(CreateProject::class)->fillForm([
            'title' => 'Nueva obra', 'slug' => 'nueva-obra', 'description' => 'Descripción', 'category' => 'Pintura', 'year' => 2026,
            'cover' => UploadedFile::fake()->image('obra.jpg', 800, 600), 'published' => false, 'featured' => false, 'sort_order' => 0,
        ])->call('create')->assertHasNoFormErrors();
        $p = Project::where('slug', 'nueva-obra')->firstOrFail();
        Storage::disk('public')->assertExists($p->cover);
        $this->get('/proyectos/nueva-obra')->assertNotFound();
        Livewire::test(EditProject::class, ['record' => $p->id])->fillForm(['published' => true])->call('save')->assertHasNoFormErrors();
        $this->get('/proyectos/nueva-obra')->assertOk();
    }

    public function test_sections_filter_projects_and_keep_drafts_private(): void
    {
        $art = $this->project();
        $teaching = Project::create(['title' => 'Taller', 'slug' => 'taller', 'description' => 'Experiencia de aula', 'category' => 'Pintura', 'section' => 'teaching', 'year' => 2026, 'published' => true]);
        Project::create(['title' => 'Borrador', 'slug' => 'borrador', 'description' => 'Privado', 'category' => 'Docencia', 'section' => 'teaching', 'year' => 2026, 'published' => false]);
        $this->get('/obra')->assertOk()->assertInertia(fn (Assert $page) => $page->component('Collection')->where('section', 'art')->has('projects', 1)->where('projects.0.id', $art->id));
        $this->get('/docencia')->assertOk()->assertInertia(fn (Assert $page) => $page->component('Collection')->where('section', 'teaching')->has('projects', 1)->where('projects.0.id', $teaching->id));
        $this->get('/sobre-mi')->assertOk()->assertInertia(fn (Assert $page) => $page->component('Information')->where('page', 'about'));
        $this->get('/contacto')->assertOk()->assertInertia(fn (Assert $page) => $page->component('Information')->where('page', 'contact'));
        $this->get('/proyectos/taller')->assertInertia(fn (Assert $page) => $page->where('project.section', 'teaching'));
    }

    public function test_home_is_a_short_selection_prioritizing_featured_projects(): void
    {
        $this->project();
        foreach (range(1, 3) as $i) {
            Project::create(['title' => 'Obra '.$i, 'slug' => 'obra-'.$i, 'description' => 'Descripción', 'category' => 'Pintura', 'year' => 2026, 'published' => true, 'featured' => $i === 3]);
        }
        $this->get('/')->assertInertia(fn (Assert $page) => $page->has('projects', 2)->where('projects.0.slug', 'obra-3'));
    }

    public function test_bulk_gallery_upload_generates_previews_and_does_not_duplicate_on_save(): void
    {
        Storage::fake('public');
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $this->actingAs($this->admin());
        Livewire::test(CreateProject::class)->fillForm([
            'title' => 'Serie', 'slug' => 'serie', 'description' => 'Una serie', 'section' => 'art', 'category' => 'Pintura', 'year' => 2026,
            'cover' => UploadedFile::fake()->image('portada.jpg', 2000, 1000),
            'bulk_images' => [UploadedFile::fake()->image('uno.jpg', 2000, 1000), UploadedFile::fake()->image('dos.png', 600, 900)],
            'published' => false, 'featured' => false, 'sort_order' => 0,
        ])->call('create')->assertHasNoFormErrors();
        $p = Project::where('slug', 'serie')->firstOrFail();
        $this->assertCount(2, $p->images);
        $disk = Storage::disk('public');
        $disk->assertExists($p->cover);
        $disk->assertExists($p->cover_preview);
        $dimensions = getimagesizefromstring($disk->get($p->cover_preview));
        $this->assertSame([1000, 500], array_slice($dimensions, 0, 2));
        foreach ($p->images as $image) {
            $disk->assertExists($image->path);
            $disk->assertExists($image->preview_path);
        }
        $edit = Livewire::test(EditProject::class, ['record' => $p->id])->fillForm([
            'bulk_images' => [UploadedFile::fake()->image('tres.webp', 900, 900)],
        ])->call('save')->assertHasNoFormErrors();
        $this->assertSame(3, $p->images()->count());
        $edit->call('save')->assertHasNoFormErrors();
        $this->assertSame(3, $p->images()->count());
    }

    public function test_public_routes_do_not_expose_private_user_data(): void
    {
        $this->admin();
        $this->get('/')->assertInertia(fn (Assert $page) => $page->missing('users')->missing('auth'));
    }

    public function test_gallery_cards_include_exhibition_details_and_keep_full_description_in_project(): void
    {
        $project = $this->project(true);
        $project->update(['description' => "Una obra.\n\nDos miradas.", 'technique' => 'Acrílico sobre papel', 'dimensions' => '30 × 40 cm']);
        $this->get('/obra')->assertInertia(fn (Assert $page) => $page
            ->where('projects.0.excerpt', 'Una obra. Dos miradas.')
            ->where('projects.0.technique', 'Acrílico sobre papel')
            ->where('projects.0.dimensions', '30 × 40 cm'));
        $this->get('/proyectos/obra-real')->assertInertia(fn (Assert $page) => $page->where('project.description', "Una obra.\n\nDos miradas."));
    }

    public function test_filtered_collection_keeps_context_and_only_links_published_projects_in_same_section(): void
    {
        $first = $this->project(true);
        $first->update(['sort_order' => 0]);
        $second = Project::create(['title' => 'Segunda obra', 'slug' => 'segunda', 'description' => 'Descripción', 'category' => 'Pintura', 'year' => 2026, 'published' => true, 'sort_order' => 1]);
        Project::create(['title' => 'Otra disciplina', 'slug' => 'otra', 'description' => 'Descripción', 'category' => 'Dibujo', 'year' => 2026, 'published' => true]);
        Project::create(['title' => 'Privado', 'slug' => 'privado', 'description' => 'Descripción', 'category' => 'Pintura', 'year' => 2026, 'published' => false]);
        Project::create(['title' => 'Docencia', 'slug' => 'docencia', 'description' => 'Descripción', 'category' => 'Pintura', 'section' => 'teaching', 'year' => 2026, 'published' => true]);
        $this->get('/obra?disciplina=Pintura')->assertInertia(fn (Assert $page) => $page->where('activeCategory', 'Pintura'));
        $this->get('/proyectos/obra-real?disciplina=Pintura')->assertInertia(fn (Assert $page) => $page->where('previous', null)->where('next.title', 'Segunda obra')->where('next.url', url('/proyectos/segunda?disciplina=Pintura'))->where('collectionUrl', url('/obra?disciplina=Pintura'))->where('total', 2));
        $this->get('/proyectos/segunda?disciplina=Pintura')->assertInertia(fn (Assert $page) => $page->where('previous.title', 'Obra real')->where('next', null)->where('position', 2));
        $this->get('/obra?disciplina=Inexistente')->assertInertia(fn (Assert $page) => $page->where('activeCategory', null));
    }
}
