<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'label',
        'description',
        'group',
        'sort_order'
    ];

    /**
     * Get setting value by key
     */
    public static function getValue($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set setting value by key
     */
    public static function setValue($key, $value)
    {
        $setting = self::where('key', $key)->first();
        if ($setting) {
            $setting->update(['value' => $value]);
        }
        return $setting;
    }

    /**
     * Get settings by group
     */
    public static function getByGroup($group)
    {
        return self::where('group', $group)->orderBy('sort_order')->get();
    }

    /**
     * Get all settings as key-value array
     */
    public static function getAllAsArray()
    {
        return self::pluck('value', 'key')->toArray();
    }
}
