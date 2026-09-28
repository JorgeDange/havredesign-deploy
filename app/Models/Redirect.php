<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Redirect extends Model
{
    use HasUuid;

    /** @var list<string> */
    protected $fillable = [
        'source_path',
        'target_path',
        'status_code',
        'active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status_code' => 'integer',
            'active' => 'boolean',
        ];
    }
}
