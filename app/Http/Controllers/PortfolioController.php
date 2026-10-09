<?php

namespace App\Http\Controllers;

use App\Models\ArtSeries;
use App\Models\Profile;
use App\Models\Project;
use App\Models\SitePage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PortfolioController extends Controller
{
    public function index()
    {
        $limit = max(1, min(8, (int) (SitePage::publicContent()['home']['featured_count'] ?? 2)));
        $projects = Project::where('published', true)->orderByDesc('featured')->orderBy('sort_order')->latest()->limit($limit)->get();

        return Inertia::render('Portfolio', ['profile' => Profile::first(), 'projects' => $this->cards($projects)]);
    }

    public function artwork()
    {
        return $this->collection('art');
    }

    public function teaching()
    {
        return $this->collection('teaching');
    }

    public function about()
    {
        return Inertia::render('Information', ['profile' => Profile::first(), 'page' => 'about']);
    }

    public function contact(): Response
    {
        $slug = request()->query('proyecto');
        $project = is_string($slug) ? Project::where('published', true)->where('slug', $slug)->first() : null;
        $context = null;

        if ($project) {
            $prefix = $project->section === 'teaching'
                ? 'Propuesta educativa: '
                : (request()->query('tipo') === 'exposicion' ? 'Propuesta de exposición: ' : 'Consulta por la obra: ');
            $context = [
                'title' => $project->title,
                'url' => route('projects.show', $project->slug),
                'subject' => mb_substr($prefix.$project->title, 0, 160),
            ];
        }

        return Inertia::render('Information', ['profile' => Profile::first(), 'page' => 'contact', 'contactReceived' => (bool) session('contact_received'), 'contactContext' => $context]);
    }

    private function collection(string $section)
    {
        $projects = Project::where('published', true)->where('section', $section)->orderBy('sort_order')->latest()->orderByDesc('id')->get();

        return Inertia::render('Collection', ['profile' => Profile::first(), 'projects' => $this->cards($projects), 'section' => $section, 'activeCategory' => $this->activeCategory($projects)]);
    }

    private function activeCategory($projects): ?string
    {
        $category = request()->query('disciplina');

        return is_string($category) && $projects->contains('category', $category) ? $category : null;
    }

    private function cards($projects)
    {
        return $projects->map(fn ($p) => [
            'id' => $p->id, 'title' => $p->title, 'slug' => $p->slug, 'category' => $p->category, 'year' => $p->year,
            'cover' => $p->cover_url, 'technique' => $p->technique, 'featured' => $p->featured,
            'dimensions' => $p->dimensions,
            'excerpt' => Str::limit(Str::squish($p->description ?? ''), 180, '…', preserveWords: true),
        ]);
    }

    public function show(string $slug)
    {
        $p = Project::where('published', true)->where('slug', $slug)->with('images')->firstOrFail();

        return $this->renderProject($p);
    }

    public function preview(Project $project)
    {
        abort_unless(auth()->user()?->is_admin, 403);

        return $this->renderProject($project->load('images'), true);
    }

    private function renderProject(Project $p, bool $preview = false)
    {
        $ordered = Project::where('published', true)->where('section', $p->section)->orderBy('sort_order')->latest()->orderByDesc('id')->get(['id', 'slug', 'title', 'category']);
        $category = $this->activeCategory($ordered);
        if ($category !== $p->category) {
            $category = null;
        }
        $seriesSlug = request()->query('serie');
        $series = is_string($seriesSlug) ? ArtSeries::where('published', true)->where('slug', $seriesSlug)->whereHas('projects', fn ($query) => $query->where('projects.id', $p->id))->first() : null;
        $collection = $category ? $ordered->where('category', $category)->values() : $ordered;
        if ($series) {
            $ids = $series->projects()->where('published', true)->where('section', 'art')->pluck('projects.id');
            $collection = $ids->map(fn ($id) => $ordered->firstWhere('id', $id))->filter()->values();
        }
        $position = $collection->search(fn ($item) => $item->id === $p->id);
        $suffix = $series ? '?'.http_build_query(['serie' => $series->slug]) : ($category ? '?'.http_build_query(['disciplina' => $category]) : '');
        $card = fn ($item) => $item ? ['title' => $item->title, 'url' => route('projects.show', $item->slug).$suffix] : null;

        return Inertia::render('Project', ['collectionLabel' => $series?->title, 'collectionUrl' => ($series ? route('series.show', $series->slug) : route($p->section === 'teaching' ? 'teaching' : 'artwork').$suffix),
            'previous' => ! $preview && $position !== false && $position > 0 ? $card($collection[$position - 1]) : null,
            'next' => ! $preview && $position !== false ? $card($collection->get($position + 1)) : null,
            'position' => ! $preview && $position !== false ? $position + 1 : null,
            'total' => ! $preview ? $collection->count() : 0,
            'project' => [
                'section' => $p->section, 'title' => $p->title, 'description' => $p->description, 'category' => $p->category, 'year' => $p->year,
                'technique' => $p->technique, 'dimensions' => $p->dimensions, 'cover' => $p->cover_url, 'cover_original' => $p->cover ? Storage::disk('public')->url($p->cover) : null,
                'images' => $p->images->map(fn ($i) => ['original' => Storage::disk('public')->url($i->path), 'url' => Storage::disk('public')->url($i->preview_path ?: $i->path), 'caption' => $i->caption, 'alt' => $i->alt]),
            ], 'profile' => Profile::first(), 'preview' => $preview]);
    }
}
