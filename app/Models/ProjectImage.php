<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ProjectImage extends Model
{
    protected $fillable = ['path','caption','alt','sort_order'];
    protected static function booted(): void {
        static::saving(function(ProjectImage $image) {
            if ($image->isDirty('path')) $image->preview_path=app(\App\Services\PortfolioImages::class)->optimize($image->path,1800);
        });
    }
    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
}
