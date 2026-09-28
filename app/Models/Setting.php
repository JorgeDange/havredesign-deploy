<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Fonte única de verdade do rodapé/menu/agenda — backend.md §4.10.
 * O valor é guardado em texto; objetos (agenda) em JSON — ver casts.
 */
class Setting extends Model
{
    use HasUuid;

    /** @var list<string> */
    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    public static function get(string $key, mixed $default = null): mixed
    {
        $value = Cache::rememberForever("setting:{$key}", function () use ($key) {
            return static::where('key', $key)->value('value');
        });

        if ($value === null) {
            return $default;
        }

        $decoded = json_decode($value, true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
    }

    public static function set(string $key, mixed $value, string $group = 'general'): self
    {
        $stored = is_string($value) ? $value : json_encode($value, JSON_UNESCAPED_UNICODE);

        $setting = static::updateOrCreate(
            ['key' => $key],
            ['value' => $stored, 'group' => $group]
        );

        Cache::forget("setting:{$key}");

        return $setting;
    }

    public static function many(array $keys): array
    {
        $out = [];
        foreach ($keys as $key) {
            $out[$key] = static::get($key);
        }

        return $out;
    }
}
