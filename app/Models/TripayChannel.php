<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripayChannel extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'group',
        'type',
        'icon_url',
        'active',
        'is_enabled',
        'fee_merchant_flat',
        'fee_merchant_percent',
        'fee_customer_flat',
        'fee_customer_percent',
        'total_fee_flat',
        'total_fee_percent',
        'minimum_fee',
        'maximum_fee',
        'minimum_amount',
        'maximum_amount',
    ];

    protected $casts = [
        'active' => 'boolean',
        'is_enabled' => 'boolean',
        'fee_merchant_percent' => 'decimal:2',
        'fee_customer_percent' => 'decimal:2',
        'total_fee_percent' => 'decimal:2',
    ];

    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeByGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    public function getFormattedFeeAttribute()
    {
        if ($this->total_fee_percent > 0) {
            return 'Rp ' . number_format($this->total_fee_flat, 0, ',', '.') . ' + ' . $this->total_fee_percent . '%';
        }
        return 'Rp ' . number_format($this->total_fee_flat, 0, ',', '.');
    }

    public function getStatusBadgeAttribute()
    {
        if (!$this->active) {
            return '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Tidak Aktif</span>';
        }
        
        if (!$this->is_enabled) {
            return '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Dinonaktifkan</span>';
        }
        
        return '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>';
    }
}
