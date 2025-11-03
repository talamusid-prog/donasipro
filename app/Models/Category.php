<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relasi ke Campaign
    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }

    // Scope untuk kategori aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope untuk urutan
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Method untuk generate slug otomatis
    public static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    // Method untuk mendapatkan URL icon
    public function getIconUrlAttribute()
    {
        if ($this->icon) {
            return asset('storage/categories/' . $this->icon);
        }
        return null;
    }

    // Method untuk mendapatkan jumlah campaign
    public function getCampaignsCountAttribute()
    {
        return $this->campaigns()->count();
    }
}
