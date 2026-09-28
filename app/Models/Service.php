<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasUuid;

    /** @var list<string> */
    protected $fillable = [
        'group',
        'title',
        'slug',
        'description',
        'icon',
        'image_url',
        'active',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function includes(): HasMany
    {
        return $this->hasMany(ServiceInclude::class, 'service_id');
    }

    public function projectRequests(): HasMany
    {
        return $this->hasMany(ProjectRequest::class, 'service_id');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopePrincipal($query)
    {
        return $query->where('group', 'principal');
    }

    public function scopeComplementar($query)
    {
        return $query->where('group', 'complementar');
    }
}
