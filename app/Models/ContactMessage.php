<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasUuid;

    /** A tabela só tem `created_at`. */
    public const UPDATED_AT = null;

    /** @var list<string> */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'privacy_consented_at',
        'status',
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
}
