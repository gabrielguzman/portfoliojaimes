<?php

namespace Tests\Feature;

use App\Filament\Resources\Disciplines\DisciplineResource;
use App\Filament\Resources\Disciplines\Pages\EditDiscipline;
use App\Filament\Resources\Profiles\Pages\EditProfile;
use App\Filament\Resources\Projects\Pages\ListProjects;
use App\Filament\Resources\SitePages\Pages\EditSitePage;
use App\Filament\Resources\SitePages\SitePageResource;
use App\Filament\Widgets\ContentGuide;
use App\Filament\Widgets\PortfolioOverview;
use App\Models\Discipline;
use App\Models\Profile;
use App\Models\Project;
use App\Models\SitePage;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Livewire\Livewire;
use Tests\TestCase;

class SiteAdministrationTest extends TestCase
{
    use RefreshDatabase;

    private function signInAdmin(): void
    {
        $u = User::factory()->create();
        $u->is_admin = true;
        $u->save();
        $this->actingAs($u);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_page_editor_updates_public_copy_and_navigation(): void
    {
        $this->signInAdmin();
        $p = SitePage::where('key', 'home')->firstOrFail();
        Livewire::test(EditSitePage::class, ['record' => $p->id])->fillForm([
            'content.heading' => 'Miradas compartidas', 'content.accent' => 'Arte y enseñanza.',
            'content.menu_label' => 'Bienvenidos', 'content.featured_count' => 4, 'seo_description' => 'Portfolio personal de arte.',
        ])->call('save')->assertHasNoFormErrors();
        $this->get('/')->assertInertia(fn (Assert $page) => $page->where('site.home.heading', 'Miradas compartidas')->where('site.home.menu_label', 'Bienvenidos')->where('site.home.featured_count', 4)->where('site.home.seo_description', 'Portfolio personal de arte.'));
        Livewire::test(EditSitePage::class, ['record' => $p->id])->fillForm(['content.featured_count' => 99])->call('save')->assertHasFormErrors(['content.featured_count']);
    }

    public function test_profile_can_upload_portrait_and_downloadable_cv(): void
    {
        Storage::fake('public');
        $this->signInAdmin();
        $p = Profile::create(['name' => 'Romina', 'intro' => 'Presentación', 'bio' => 'Biografía']);
        Livewire::test(EditProfile::class, ['record' => $p->id])->fillForm([
            'name' => 'Romina Jaimes', 'role' => 'Artista y docente', 'brand_subtitle' => 'ARTES VISUALES', 'footer_text' => 'Arte y encuentro.',
            'portrait' => UploadedFile::fake()->image('romina.jpg', 1800, 1200), 'portrait_alt' => 'Retrato de Romina',
            'cv' => UploadedFile::fake()->createWithContent('cv.pdf', "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\n%%EOF"),
        ])->call('save')->assertHasNoFormErrors();
        $p->refresh();
        Storage::disk('public')->assertExists($p->portrait);
        Storage::disk('public')->assertExists($p->portrait_preview);
        Storage::disk('public')->assertExists($p->cv);
        $this->get('/sobre-mi')->assertInertia(fn (Assert $page) => $page->where('profile.role', 'Artista y docente')->where('profile.portrait_alt', 'Retrato de Romina')->where('profile.cv_url', route('curriculum')));
        $this->get('/curriculum')->assertOk()->assertDownload('curriculum.pdf');
    }

    public function test_missing_cv_is_not_found(): void
    {
        $this->get('/curriculum')->assertNotFound();
    }

    public function test_renaming_a_discipline_keeps_projects_connected_and_prevents_deletion(): void
    {
        $this->signInAdmin();
        $d = Discipline::where('name', 'Pintura')->firstOrFail();
        $p = Project::create(['title' => 'Obra', 'slug' => 'obra', 'description' => 'Descripción', 'category' => 'Pintura', 'year' => 2026]);
        Livewire::test(EditDiscipline::class, ['record' => $d->id])->fillForm(['name' => 'Pintura y color'])->call('save')->assertHasNoFormErrors();
        $this->assertSame('Pintura y color', $p->fresh()->category);
        $this->assertFalse(DisciplineResource::canDelete($d->fresh()));
    }

    public function test_project_trash_and_restore_preserve_images_and_return_a_draft(): void
    {
        $this->signInAdmin();
        $p = Project::create(['title' => 'Obra recuperable', 'slug' => 'recuperable', 'description' => 'Descripción', 'category' => 'Pintura', 'year' => 2026, 'published' => true]);
        $p->images()->create(['path' => 'demo/territorios.svg', 'alt' => 'Imagen conservada', 'sort_order' => 0]);
        Livewire::test(ListProjects::class)->callTableAction('delete', $p)->assertHasNoTableActionErrors();
        $this->assertSoftDeleted($p);
        $this->get('/proyectos/recuperable')->assertNotFound();
        $this->assertSame(1, $p->images()->count());
        $d = Discipline::where('name', 'Pintura')->firstOrFail();
        $this->assertFalse(DisciplineResource::canDelete($d));
        Livewire::test(EditDiscipline::class, ['record' => $d->id])->fillForm(['name' => 'Pintura y proceso'])->call('save')->assertHasNoFormErrors();
        $this->assertSame('Pintura y proceso', Project::withTrashed()->find($p->id)->category);
        Livewire::test(ListProjects::class)->filterTable('trashed', false)->callTableAction('restore', Project::withTrashed()->find($p->id))->assertHasNoTableActionErrors();
        $restored = Project::findOrFail($p->id);
        $this->assertFalse($restored->published);
        $this->assertCount(1, $restored->images);
        $this->get('/proyectos/recuperable')->assertNotFound();
        $restored->update(['published' => true]);
        $this->get('/proyectos/recuperable')->assertOk();
    }

    public function test_new_admin_sections_are_protected_and_dashboard_renders(): void
    {
        foreach (['/admin/site-pages', '/admin/disciplines', '/admin/profiles'] as $path) {
            $this->get($path)->assertRedirect('/admin/login');
        }
        $this->actingAs(User::factory()->create())->get('/admin/site-pages')->assertForbidden();
        $this->signInAdmin();
        $this->get('/admin/site-pages/create')->assertForbidden();
        Profile::create(['name' => 'Romina', 'intro' => 'Presentación', 'bio' => 'Biografía']);
        $this->get('/admin/profiles/create')->assertForbidden();
        foreach (['/admin', '/admin/site-pages', '/admin/disciplines', '/admin/profiles', '/admin/profile'] as $path) {
            $this->get($path)->assertOk();
        }
        Livewire::test(ContentGuide::class)->assertSee('Administrar el sitio')->assertSee('canal de contacto')->assertSee('Crear proyecto')->assertSee('Subir imágenes')->assertSeeHtml('href="'.SitePageResource::getUrl('edit', ['record' => SitePage::where('key', 'home')->firstOrFail()]).'"');
        Livewire::test(PortfolioOverview::class)->assertSee('Proyectos publicados');
    }
}
