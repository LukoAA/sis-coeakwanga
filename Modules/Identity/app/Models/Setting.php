<?php

namespace Modules\Identity\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Read a setting, decoding its stored JSON. Returns $default if absent.
     * Values are stored JSON-encoded so any scalar, array, or null round-trips cleanly.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $row = static::query()->where('key', $key)->first();

        return $row ? json_decode($row->value, true) : $default;
    }

    /** Create or update a setting, JSON-encoding the value. */
    public static function put(string $key, mixed $value): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => json_encode($value)],
        );
    }
}
