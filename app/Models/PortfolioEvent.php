<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortfolioEvent extends Model
{
    use HasFactory;

    protected $table = 'portfolio_events';

    protected $fillable = ['title', 'slug', 'description', 'kind', 'starts_on', 'ends_on', 'location', 'address', 'cover', 'published'];

    protected function casts(): array
    {
        return ['published' => 'boolean', 'starts_on' => 'date', 'ends_on' => 'date'];
    }
}
