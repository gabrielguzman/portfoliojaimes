<?php

namespace Tests\Feature;

use App\Models\ArtSeries;
use App\Models\PortfolioEvent;
use App\Models\Project;
use App\Models\TeachingMaterial;
use Database\Seeders\PortfolioContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PortfolioContentSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_sample_content_fills_the_new_sections_and_downloads_are_valid(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        $this->seed(PortfolioContentSeeder::class);
        $this->assertSame(6, Project::count());
        $this->assertSame(2, ArtSeries::count());
        $this->assertSame(4, PortfolioEvent::count());
        $this->assertSame(3, TeachingMaterial::count());
        $this->get('/series')->assertInertia(fn (Assert $page) => $page->has('items', 2));
        $this->get('/series/territorios-de-la-mirada')->assertInertia(fn (Assert $page) => $page->has('projects', 3));
        $this->get('/docencia')->assertInertia(fn (Assert $page) => $page->has('projects', 2));
        $this->get('/agenda')->assertInertia(fn (Assert $page) => $page->has('items', 4)->where('items.0.past', true)->where('items.3.past', false));
        $this->get('/docencia/materiales')->assertInertia(fn (Assert $page) => $page->has('items', 3));
        foreach (TeachingMaterial::all() as $material) {
            $this->assertStringStartsWith('%PDF-', Storage::disk('local')->get($material->file));
            $this->get('/docencia/materiales/'.$material->slug.'/descargar')->assertDownload($material->slug.'.pdf');
        }
    }

    public function test_running_again_preserves_edits_files_membership_and_deleted_projects(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        $this->seed(PortfolioContentSeeder::class);
        $project = Project::where('slug', 'paisaje-interior')->firstOrFail();
        $project->update(['description' => 'Mi descripción real', 'published' => false]);
        $series = ArtSeries::where('slug', 'territorios-de-la-mirada')->firstOrFail();
        $series->update(['title' => 'Mi colección']);
        $series->projects()->detach($project);
        $event = PortfolioEvent::first();
        $event->update(['starts_on' => '2030-01-01', 'published' => false]);
        $material = TeachingMaterial::first();
        $material->update(['title' => 'Guía revisada']);
        Storage::disk('local')->put($material->file, '%PDF-revisado');
        Project::where('slug', 'linea-en-transito')->firstOrFail()->delete();
        $this->seed(PortfolioContentSeeder::class);
        $this->assertSame(6, Project::withTrashed()->count());
        $this->assertSame('Mi descripción real', $project->fresh()->description);
        $this->assertFalse($project->fresh()->published);
        $this->assertSame('Mi colección', $series->fresh()->title);
        $this->assertSame(1, $series->projects()->count());
        $this->assertSame('2030-01-01', $event->fresh()->starts_on->format('Y-m-d'));
        $this->assertFalse($event->fresh()->published);
        $this->assertSame('Guía revisada', $material->fresh()->title);
        $this->assertSame('%PDF-revisado', Storage::disk('local')->get($material->file));
    }
}
