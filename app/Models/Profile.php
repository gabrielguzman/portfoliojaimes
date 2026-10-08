<?php

namespace App\Models;

use App\Services\PortfolioImages;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Profile extends Model
{
    protected $appends = ['portrait_url', 'cv_url'];

    protected static function booted(): void
    {
        static::saving(function (Profile $profile) {
            if ($profile->isDirty('portrait')) {
                $profile->portrait_preview = app(PortfolioImages::class)->optimize($profile->portrait, 1400);
            }
        });
    }

    public function getPortraitUrlAttribute(): ?string
    {
        return $this->portrait ? Storage::disk('public')->url($this->portrait_preview ?: $this->portrait) : null;
    }

    public function getCvUrlAttribute(): ?string
    {
        return $this->cv ? route('curriculum') : null;
    }

    protected function casts(): array
    {
        return ['trajectory' => 'array'];
    }

    protected $fillable = ['name', 'intro', 'bio', 'email', 'instagram', 'location', 'role', 'brand_subtitle', 'footer_text', 'portrait', 'portrait_alt', 'cv', 'teaching_statement', 'trajectory'];
}
