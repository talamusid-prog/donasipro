<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'image_url',
        'category_id',
        'sections',
        'target_amount',
        'current_amount',
        'start_date',
        'end_date',
        'status',
        'is_verified',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'track_conversions',
        'track_engagement',
        'enhanced_ecommerce',
        'analytics_metadata',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'target_amount' => 'integer',
        'current_amount' => 'integer',
        'is_verified' => 'boolean',
        'sections' => 'array',
        'track_conversions' => 'boolean',
        'track_engagement' => 'boolean',
        'enhanced_ecommerce' => 'boolean',
        'analytics_metadata' => 'array',
    ];

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getProgressPercentageAttribute()
    {
        if ($this->target_amount <= 0) {
            return 0;
        }
        
        return min(($this->current_amount / $this->target_amount) * 100, 100);
    }

    public function getDaysLeftAttribute()
    {
        $daysLeft = ceil((strtotime($this->end_date) - time()) / (60 * 60 * 24));
        return $daysLeft > 0 ? $daysLeft : 0;
    }

    // Helper method untuk mendapatkan label kategori
    public function getCategoryLabelAttribute()
    {
        $categories = [
            'yatim-dhuafa' => 'Yatim & Dhuafa',
            'medical' => 'Bantuan Medis',
            'education' => 'Pendidikan',
            'mosque' => 'Masjid',
            'disaster' => 'Bencana Alam',
            'orphanage' => 'Panti Asuhan',
            'hospital' => 'Rumah Sakit',
            'school' => 'Sekolah',
            'community' => 'Masyarakat',
            'environment' => 'Lingkungan',
            'animal' => 'Hewan',
            'other' => 'Lainnya'
        ];

        return $categories[$this->category] ?? $this->category;
    }

    // Helper method untuk mendapatkan label section
    public function getSectionLabelAttribute()
    {
        $sections = [
            'featured' => 'Unggulan',
            'urgent' => 'Urgent',
            'popular' => 'Populer',
            'new' => 'Baru',
            'ending_soon' => 'Berakhir Segera'
        ];

        return $sections[$this->section] ?? $this->section;
    }

    // Helper method untuk mendapatkan semua kategori
    public static function getCategories()
    {
        return [
            'yatim-dhuafa' => 'Yatim & Dhuafa',
            'medical' => 'Bantuan Medis',
            'education' => 'Pendidikan',
            'mosque' => 'Masjid',
            'disaster' => 'Bencana Alam',
            'orphanage' => 'Panti Asuhan',
            'hospital' => 'Rumah Sakit',
            'school' => 'Sekolah',
            'community' => 'Masyarakat',
            'environment' => 'Lingkungan',
            'animal' => 'Hewan',
            'other' => 'Lainnya'
        ];
    }

    // Helper method untuk mendapatkan semua section
    public static function getSections()
    {
        return [
            'featured' => 'Unggulan',
            'new' => 'Baru',
            'other' => 'Lainnya'
        ];
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get analytics tracking data for this campaign
     */
    public function getAnalyticsData()
    {
        return [
            'campaign_id' => $this->id,
            'campaign_title' => $this->title,
            'campaign_category' => $this->category?->name ?? $this->getCategoryLabelAttribute(),
            'target_amount' => $this->target_amount,
            'current_amount' => $this->current_amount,
            'progress_percentage' => $this->progress_percentage,
            'utm_source' => $this->utm_source,
            'utm_medium' => $this->utm_medium,
            'utm_campaign' => $this->utm_campaign,
            'track_conversions' => $this->track_conversions,
            'track_engagement' => $this->track_engagement,
            'enhanced_ecommerce' => $this->enhanced_ecommerce,
        ];
    }

    /**
     * Get UTM parameters as array
     */
    public function getUtmParameters()
    {
        $params = [];
        
        if ($this->utm_source) {
            $params['utm_source'] = $this->utm_source;
        }
        if ($this->utm_medium) {
            $params['utm_medium'] = $this->utm_medium;
        }
        if ($this->utm_campaign) {
            $params['utm_campaign'] = $this->utm_campaign;
        }
        
        return $params;
    }

    /**
     * Check if campaign has analytics tracking enabled
     */
    public function hasAnalyticsTracking()
    {
        return $this->track_conversions || $this->track_engagement || $this->enhanced_ecommerce;
    }

    /**
     * Get campaign URL with UTM parameters
     */
    public function getTrackingUrl()
    {
        $baseUrl = route('campaigns.show', $this->slug);
        $utmParams = $this->getUtmParameters();
        
        if (empty($utmParams)) {
            return $baseUrl;
        }
        
        return $baseUrl . '?' . http_build_query($utmParams);
    }
} 