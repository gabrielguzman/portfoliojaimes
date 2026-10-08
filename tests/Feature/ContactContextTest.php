<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ContactContextTest extends TestCase
{
    use RefreshDatabase;

    private function project(array $attributes = []): Project
    {
        return Project::create([...[
            'title' => 'Territorios sensibles', 'slug' => 'territorios', 'section' => 'art',
            'category' => 'Pintura', 'year' => 2026, 'description' => 'Proyecto para la prueba.', 'published' => true,
        ], ...$attributes]);
    }

    public function test_artwork_consultation_uses_the_published_title_and_editable_subject(): void
    {
        $project = $this->project();

        $this->get('/contacto?proyecto=territorios&title=Texto+ajeno')->assertInertia(fn (Assert $page) => $page
            ->where('contactContext.title', $project->title)
            ->where('contactContext.subject', 'Consulta por la obra: Territorios sensibles')
            ->where('contactContext.url', route('projects.show', $project->slug)));
    }

    public function test_exhibition_consultation_has_a_specific_subject(): void
    {
        $this->project();

        $this->get('/contacto?proyecto=territorios&tipo=exposicion')->assertInertia(fn (Assert $page) => $page
            ->where('contactContext.subject', 'Propuesta de exposición: Territorios sensibles'));
    }

    public function test_teaching_projects_always_prepare_an_educational_proposal(): void
    {
        $this->project(['section' => 'teaching']);

        $this->get('/contacto?proyecto=territorios&tipo=exposicion')->assertInertia(fn (Assert $page) => $page
            ->where('contactContext.subject', 'Propuesta educativa: Territorios sensibles'));
    }

    public function test_unpublished_deleted_and_unknown_projects_do_not_expose_context(): void
    {
        $project = $this->project(['published' => false]);
        $this->get('/contacto?proyecto=territorios')->assertInertia(fn (Assert $page) => $page->where('contactContext', null));
        $project->update(['published' => true]);
        $project->delete();

        $this->get('/contacto?proyecto=territorios')->assertInertia(fn (Assert $page) => $page->where('contactContext', null));
        $this->get('/contacto?proyecto=inexistente')->assertInertia(fn (Assert $page) => $page->where('contactContext', null));
        $this->get('/contacto?proyecto[]=territorios')->assertInertia(fn (Assert $page) => $page->where('contactContext', null));
        $this->get('/contacto')->assertInertia(fn (Assert $page) => $page->where('contactContext', null));
    }

    public function test_long_titles_produce_a_subject_within_the_form_limit(): void
    {
        $this->project(['title' => str_repeat('á', 180)]);

        $this->get('/contacto?proyecto=territorios&tipo=invalid')->assertInertia(fn (Assert $page) => $page
            ->where('contactContext.subject', fn ($subject) => mb_strlen($subject) === 160 && str_starts_with($subject, 'Consulta por la obra: ')));
    }
}
