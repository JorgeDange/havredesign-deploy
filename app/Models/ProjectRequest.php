<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectRequest extends Model
{
    use HasUuid;

    /** @var list<string> */
    protected $fillable = [
        'user_id',
        'user_name',
        'user_email',
        'user_phone',
        'address',
        'location',
        'area_approx',
        'project_stage',
        'service_id',
        'solution_id',
        'segment',
        'preferred_channel',
        'preferred_time',
        'project_type',
        'budget',
        'timeline',
        'description',
        'status',
        'privacy_consented_at',
        'privacy_version',
        'ip_address',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'privacy_consented_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function solution(): BelongsTo
    {
        return $this->belongsTo(Solution::class, 'solution_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class, 'request_id');
    }
}
