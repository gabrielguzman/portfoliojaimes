<?php

namespace Tests\Feature;

use App\Models\ArtSeries;
use App\Models\PortfolioEvent;
use App\Models\Profile;
use App\Models\Project;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\PortfolioShowcaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PortfolioShowcaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_showcase_completes_the_profile_and_replaces_initial_demos_with_illustrated_content(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        $this->seed(DatabaseSeeder::class);
        $this->seed(PortfolioShowcaseSeeder::class);
        $profile = Profile::firstOrFail();
        $this->assertCount(3, $profile->trajectory);
        $this->assertStringNotContainsString('se completará', $profile->bio);
        $this->assertStringContainsString('Muestra ficticia', $profile->footer_text);
        Storage::disk('public')->assertExists($profile->portrait_preview);
        $this->assertSame(6, Project::where('published', true)->count());
        $this->assertFalse(Project::where('slug', 'territorios')->firstOrFail()->published);
        $project = Project::where('slug', 'paisaje-interior')->firstOrFail();
        $this->assertCount(2, $project->images);
        $this->assertStringNotContainsString('Contenido de ejemplo', $project->description);
        Storage::disk('public')->assertExists($project->cover_preview);
        $this->assertSame('Sala Umbral', PortfolioEvent::where('slug', 'muestra-territorios')->firstOrFail()->location);
        $this->get('/')->assertInertia(fn (Assert $page) => $page->where('projects.0.slug', 'paisaje-interior')->has('projects', 4));
        $this->get('/obra')->assertInertia(fn (Assert $page) => $page->has('projects', 4));
    }

    public function test_repeating_showcase_does_not_overwrite_later_edits(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        $this->seed(PortfolioShowcaseSeeder::class);
        $profile = Profile::firstOrFail();
        $profile->update(['bio' => 'Mi presentación revisada']);
        $project = Project::where('slug', 'paisaje-interior')->firstOrFail();
        $project->update(['description' => 'Mi texto', 'published' => false]);
        $project->images()->first()->update(['caption' => 'Mi pie de imagen']);
        $event = PortfolioEvent::where('slug', 'muestra-territorios')->firstOrFail();
        $event->update(['location' => 'Mi sala']);
        $series = ArtSeries::firstOrFail();
        $series->update(['title' => 'Mi colección']);
        $this->seed(PortfolioShowcaseSeeder::class);
        $this->assertSame('Mi presentación revisada', $profile->fresh()->bio);
        $this->assertSame('Mi texto', $project->fresh()->description);
        $this->assertFalse($project->fresh()->published);
        $this->assertSame('Mi pie de imagen', $project->images()->first()->caption);
        $this->assertSame('Mi sala', $event->fresh()->location);
        $this->assertSame('Mi colección', $series->fresh()->title);
        $this->assertSame(6, Project::count());
    }

    public function test_showcase_preserves_an_existing_personal_biography(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        Profile::create(['name' => 'Romina', 'intro' => 'Mi presentación', 'bio' => 'Una biografía ya escrita', 'portrait' => 'portraits/personal.jpg']);
        $this->seed(PortfolioShowcaseSeeder::class);
        $this->assertSame('Una biografía ya escrita', Profile::firstOrFail()->bio);
        $this->assertSame('portraits/personal.jpg', Profile::firstOrFail()->portrait);
    }
}
