<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Solution extends Model
{
    use HasUuid;

    /** @var list<string> */
    protected $fillable = [
        'code',
        'name',
        'slug',
        'description',
        'cta_label',
        'sort_order',
        'active',
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

    public function projectRequests(): HasMany
    {
        return $this->hasMany(ProjectRequest::class, 'solution_id');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
