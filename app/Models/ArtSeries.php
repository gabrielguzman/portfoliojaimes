<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ArtSeries extends Model
{
    use HasFactory;

    protected $table = 'art_series';

    protected $fillable = ['title', 'slug', 'description', 'cover', 'published', 'sort_order'];

    protected function casts(): array
    {
        return ['published' => 'boolean'];
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'art_series_project')->orderBy('projects.sort_order')->orderBy('projects.id');
    }
}
