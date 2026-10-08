<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
class Project extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    protected $fillable = ['section','title','slug','description','category','year','technique','dimensions','cover','published','featured','sort_order'];
    protected function casts(): array { return ['published'=>'boolean','featured'=>'boolean','year'=>'integer']; }
    protected static function booted(): void {
        static::restoring(function(Project $project) { $project->published=false; });
        static::saving(function(Project $project) {
            if ($project->isDirty('cover')) $project->cover_preview=app(\App\Services\PortfolioImages::class)->optimize($project->cover,1000);
        });
    }
    public function images(): HasMany { return $this->hasMany(ProjectImage::class)->orderBy('sort_order'); }
    public function getCoverUrlAttribute(): ?string { return $this->cover ? Storage::disk('public')->url($this->cover_preview ?: $this->cover) : null; }
}
