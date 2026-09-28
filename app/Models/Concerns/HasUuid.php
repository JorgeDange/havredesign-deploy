<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

/**
 * IDs VARCHAR(36) + DEFAULT (UUID()) — laravel.md §6.7 / backend.md §4.1.
 * - `initializeHasUuid`: chave string, não auto-incremental.
 * - `bootHasUuid`: gera o UUID no `creating` (a BD também tem DEFAULT uuid()).
 */
trait HasUuid
{
    protected function initializeHasUuid(): void
    {
        $this->keyType = 'string';
        $this->incrementing = false;
    }

    protected static function bootHasUuid(): void
    {
        static::creating(function ($model): void {
            if (! $model->getKey()) {
                $model->setAttribute($model->getKeyName(), (string) Str::uuid());
            }
        });
    }
}
