<?php

namespace App\Http\Controllers;

use App\Models\ArtSeries;
use App\Models\PortfolioEvent;
use App\Models\Profile;
use App\Models\TeachingMaterial;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EditorialController extends Controller
{
    public function series(): Response
    {
        return Inertia::render('Editorial', ['profile' => Profile::first(), 'kind' => 'series', 'items' => ArtSeries::where('published', true)->withCount(['projects' => fn ($query) => $query->where('published', true)->where('section', 'art')])->orderBy('sort_order')->orderBy('id')->get()->map(fn ($series) => ['title' => $series->title, 'slug' => $series->slug, 'description' => $series->description, 'cover' => $series->cover ? Storage::disk('public')->url($series->cover) : null, 'count' => $series->projects_count])]);
    }

    public function showSeries(string $slug): Response
    {
        $series = ArtSeries::where('published', true)->where('slug', $slug)->firstOrFail();
        $projects = $series->projects()->where('published', true)->where('section', 'art')->get();

        return Inertia::render('Series', ['profile' => Profile::first(), 'series' => ['title' => $series->title, 'description' => $series->description], 'projects' => $projects->map(fn ($p) => ['id' => $p->id, 'title' => $p->title, 'slug' => $p->slug, 'category' => $p->category, 'year' => $p->year, 'cover' => $p->cover_url, 'technique' => $p->technique, 'featured' => $p->featured, 'dimensions' => $p->dimensions]), 'seriesSlug' => $series->slug]);
    }

    public function agenda(): Response
    {
        return Inertia::render('Editorial', ['profile' => Profile::first(), 'kind' => 'agenda', 'items' => PortfolioEvent::where('published', true)->orderBy('starts_on')->orderBy('id')->get()->map(fn ($event) => ['title' => $event->title, 'slug' => $event->slug, 'description' => $event->description, 'kind' => ['exhibition' => 'Exposición', 'workshop' => 'Taller', 'activity' => 'Actividad'][$event->kind] ?? 'Actividad', 'starts_on' => $event->starts_on->format('Y-m-d'), 'ends_on' => $event->ends_on?->format('Y-m-d'), 'date_label' => $event->starts_on->format('d/m/Y').($event->ends_on && ! $event->ends_on->equalTo($event->starts_on) ? ' — '.$event->ends_on->format('d/m/Y') : ''), 'past' => ($event->ends_on ?? $event->starts_on)->format('Y-m-d') < today('America/Argentina/Catamarca')->format('Y-m-d'), 'location' => $event->location, 'address' => $event->address, 'cover' => $event->cover ? Storage::disk('public')->url($event->cover) : null])]);
    }

    public function materials(): Response
    {
        return Inertia::render('Editorial', ['profile' => Profile::first(), 'kind' => 'materials', 'items' => TeachingMaterial::where('published', true)->orderBy('sort_order')->orderBy('id')->get()->filter(fn ($material) => Storage::disk('local')->exists($material->file))->values()->map(fn ($material) => ['title' => $material->title, 'slug' => $material->slug, 'description' => $material->description, 'audience' => $material->audience, 'download' => route('materials.download', $material->slug)])]);
    }

    public function download(string $slug): StreamedResponse
    {
        $material = TeachingMaterial::where('published', true)->where('slug', $slug)->firstOrFail();
        abort_unless(Storage::disk('local')->exists($material->file), 404);

        return Storage::disk('local')->download($material->file, $material->slug.'.pdf', ['Content-Type' => 'application/pdf', 'X-Content-Type-Options' => 'nosniff']);
    }
}
