<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeachingMaterial extends Model
{
    use HasFactory;

    protected $table = 'teaching_materials';

    protected $fillable = ['title', 'slug', 'description', 'audience', 'file', 'published', 'sort_order'];

    protected function casts(): array
    {
        return ['published' => 'boolean'];
    }
}
