<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsAppTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'title',
        'template',
        'variables',
        'is_active',
        'description'
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean'
    ];

    /**
     * Get template by type
     */
    public static function getByType($type)
    {
        return static::where('type', $type)
                    ->where('is_active', true)
                    ->first();
    }

    /**
     * Get all active templates
     */
    public static function getActiveTemplates()
    {
        return static::where('is_active', true)->get();
    }

    /**
     * Get available variables for this template
     */
    public function getAvailableVariables()
    {
        return $this->variables ?? [];
    }

    /**
     * Replace variables in template
     */
    public function replaceVariables($data)
    {
        $template = $this->template;
        
        foreach ($data as $key => $value) {
            $template = str_replace('{' . $key . '}', $value, $template);
        }
        
        return $template;
    }
}
