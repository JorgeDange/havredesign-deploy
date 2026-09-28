<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PortfolioItem extends Model
{
    use HasUuid;

    /** @var list<string> */
    protected $fillable = [
        'title',
        'slug',
        'category',
        'status',
        'segment',
        'description',
        'area',
        'year',
        'location',
        'image_url',
        'featured',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'featured' => 'boolean',
            'sort_order' => 'integer',
            'year' => 'integer',
        ];
    }

    public function gallery(): HasMany
    {
        return $this->hasMany(PortfolioGallery::class, 'portfolio_id');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }
}
