<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasUuid;

    /** @var list<string> */
    protected $fillable = [
        'user_id',
        'user_name',
        'user_email',
        'user_phone',
        'type',
        'address',
        'meeting_link',
        'appt_date',
        'appt_time',
        'notes',
        'timezone',
        'status',
        'confirmed_at',
        'cancelled_at',
        'fee_note',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'appt_date' => 'date',
            'confirmed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
