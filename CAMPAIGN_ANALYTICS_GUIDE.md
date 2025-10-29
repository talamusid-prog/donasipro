# 📊 Campaign Analytics Integration Guide

## 🎯 Overview

Fitur analytics campaign memungkinkan tracking yang lebih detail untuk setiap campaign donasi dengan integrasi Google Analytics dan Facebook Pixel yang sudah ada.

## ✨ Fitur Utama

### 1. **Global Analytics (Sudah Aktif)**
- ✅ Google Analytics tracking di semua halaman
- ✅ Facebook Pixel tracking di semua halaman
- ✅ Event tracking otomatis untuk donasi dan campaign views

### 2. **Campaign-Specific Analytics (Baru)**
- 🔄 UTM parameter tracking
- 🔄 Campaign-specific event tracking
- 🔄 Enhanced e-commerce tracking
- 🔄 Engagement tracking

## 🛠 Implementasi

### Database Schema

```sql
-- Field analytics baru di tabel campaigns
ALTER TABLE campaigns ADD COLUMN utm_source VARCHAR(100) NULL;
ALTER TABLE campaigns ADD COLUMN utm_medium VARCHAR(100) NULL;
ALTER TABLE campaigns ADD COLUMN utm_campaign VARCHAR(100) NULL;
ALTER TABLE campaigns ADD COLUMN track_conversions BOOLEAN DEFAULT TRUE;
ALTER TABLE campaigns ADD COLUMN track_engagement BOOLEAN DEFAULT TRUE;
ALTER TABLE campaigns ADD COLUMN enhanced_ecommerce BOOLEAN DEFAULT TRUE;
ALTER TABLE campaigns ADD COLUMN analytics_metadata JSON NULL;
```

### Model Campaign

```php
// Field fillable
protected $fillable = [
    // ... existing fields
    'utm_source',
    'utm_medium', 
    'utm_campaign',
    'track_conversions',
    'track_engagement',
    'enhanced_ecommerce',
    'analytics_metadata',
];

// Casts
protected $casts = [
    // ... existing casts
    'track_conversions' => 'boolean',
    'track_engagement' => 'boolean',
    'enhanced_ecommerce' => 'boolean',
    'analytics_metadata' => 'array',
];
```

### Methods Analytics

```php
// Get analytics data untuk tracking
public function getAnalyticsData()
{
    return [
        'campaign_id' => $this->id,
        'campaign_title' => $this->title,
        'campaign_category' => $this->category?->name,
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

// Get UTM parameters
public function getUtmParameters()
{
    $params = [];
    if ($this->utm_source) $params['utm_source'] = $this->utm_source;
    if ($this->utm_medium) $params['utm_medium'] = $this->utm_medium;
    if ($this->utm_campaign) $params['utm_campaign'] = $this->utm_campaign;
    return $params;
}

// Get tracking URL dengan UTM
public function getTrackingUrl()
{
    $baseUrl = route('campaigns.show', $this->slug);
    $utmParams = $this->getUtmParameters();
    return empty($utmParams) ? $baseUrl : $baseUrl . '?' . http_build_query($utmParams);
}
```

## 🎨 UI/UX Implementation

### Form Create Campaign

Section analytics ditambahkan di halaman create campaign dengan:

1. **Info Panel** - Menjelaskan analytics global yang sudah aktif
2. **UTM Parameters** - Field untuk source, medium, dan campaign
3. **Tracking Options** - Checkbox untuk enable/disable tracking
4. **Analytics Preview** - Status analytics global

### Layout

```html
<!-- Analytics & Tracking Section -->
<div class="border-t border-gray-200 pt-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">
        <i data-lucide="bar-chart-3" class="w-5 h-5 mr-2 text-blue-600"></i>
        Analytics & Tracking
    </h3>
    
    <!-- Info Panel -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <!-- Analytics info -->
    </div>
    
    <!-- UTM Parameters -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- UTM fields -->
    </div>
    
    <!-- Tracking Options -->
    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
        <!-- Checkboxes -->
    </div>
    
    <!-- Analytics Preview -->
    <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
        <!-- Status preview -->
    </div>
</div>
```

## 📊 Event Tracking

### Events yang Di-track

1. **campaign_created** - Saat campaign dibuat
2. **campaign_updated** - Saat campaign diupdate
3. **campaign_view** - Saat campaign dilihat
4. **donation_started** - Saat user mulai donasi
5. **donation_completed** - Saat donasi selesai
6. **campaign_engagement** - Saat user berinteraksi dengan campaign

### Data yang Di-track

```php
$analyticsData = [
    'campaign_id' => 1,
    'campaign_title' => 'Bantuan Bencana Alam',
    'campaign_category' => 'Bencana Alam',
    'target_amount' => 10000000,
    'current_amount' => 5000000,
    'progress_percentage' => 50.0,
    'utm_source' => 'facebook',
    'utm_medium' => 'social',
    'utm_campaign' => 'ramadan2024',
    'track_conversions' => true,
    'track_engagement' => true,
    'enhanced_ecommerce' => true,
];
```

## 🧪 Testing

### Command Testing

```bash
# Test analytics campaign default
php artisan analytics:test-campaign

# Test dengan campaign ID spesifik
php artisan analytics:test-campaign 1

# Test event spesifik
php artisan analytics:test-campaign 1 --event=donation
php artisan analytics:test-campaign 1 --event=conversion
php artisan analytics:test-campaign 1 --event=engagement
```

### Output Testing

```
🔍 Testing Campaign Analytics...

📊 Campaign: Bantuan Bencana Alam
🆔 ID: 1
📈 Progress: 50%

⚙️  Analytics Settings:
+------------------+----------+
| Setting          | Value    |
+------------------+----------+
| Track Conversions| ✅ Yes   |
| Track Engagement | ✅ Yes   |
| Enhanced E-commerce| ✅ Yes |
| UTM Source       | facebook |
| UTM Medium       | social   |
| UTM Campaign     | ramadan2024|
+------------------+----------+

🌐 Global Analytics Status:
+-------------------+----------+
| Service           | Status   |
+-------------------+----------+
| Google Analytics  | ✅ Active|
| Facebook Pixel    | ✅ Active|
+-------------------+----------+

🎯 Testing Event: view
✅ Campaign view event tracked successfully

📋 Analytics Data Sent:
+----------------------+------------------+
| Key                  | Value            |
+----------------------+------------------+
| campaign_id          | 1                |
| campaign_title       | Bantuan Bencana Alam|
| campaign_category    | Bencana Alam     |
| target_amount        | 10000000         |
| current_amount       | 5000000          |
| progress_percentage  | 50               |
| utm_source           | facebook         |
| utm_medium           | social           |
| utm_campaign         | ramadan2024      |
| track_conversions    | Yes              |
| track_engagement     | Yes              |
| enhanced_ecommerce   | Yes              |
+----------------------+------------------+

🔗 Campaign Tracking URL:
http://donasi-apps.test/campaigns/bantuan-bencana-alam?utm_source=facebook&utm_medium=social&utm_campaign=ramadan2024

✅ Campaign analytics test completed successfully!
```

## 🎯 Cara Penggunaan

### 1. **Buat Campaign dengan Analytics**

1. Buka halaman `/admin/campaigns/create`
2. Isi form campaign seperti biasa
3. Scroll ke section "Analytics & Tracking"
4. Isi UTM parameters (opsional):
   - **UTM Source**: facebook, google, email, etc.
   - **UTM Medium**: cpc, social, email, etc.
   - **UTM Campaign**: ramadan2024, qurban2024, etc.
5. Aktifkan tracking options yang diinginkan
6. Submit form

### 2. **Monitor Analytics**

1. **Google Analytics**:
   - Buka Google Analytics
   - Lihat Events > Custom Events
   - Filter by event name: campaign_view, donation_completed, etc.

2. **Facebook Pixel**:
   - Buka Facebook Events Manager
   - Lihat Events > Custom Events
   - Filter by event name

### 3. **Gunakan Tracking URL**

Campaign akan otomatis generate tracking URL dengan UTM parameters:

```
http://donasi-apps.test/campaigns/nama-campaign?utm_source=facebook&utm_medium=social&utm_campaign=ramadan2024
```

## 🔧 Konfigurasi

### Environment Variables

```env
# Google Analytics
GOOGLE_ANALYTICS_ID=G-XXXXXXXXXX

# Facebook Pixel
FACEBOOK_PIXEL_ID=XXXXXXXXXX
```

### App Settings

```php
// Di admin settings
'google_analytics_id' => 'G-XXXXXXXXXX',
'facebook_pixel_id' => 'XXXXXXXXXX',
'analytics_enabled' => true,
```

## 📈 Manfaat Bisnis

### 1. **Campaign Performance Tracking**
- Track konversi per campaign
- Analisis source traffic terbaik
- Optimasi campaign berdasarkan data

### 2. **User Behavior Analysis**
- Track user journey dari view ke donasi
- Analisis engagement per campaign
- Identifikasi drop-off points

### 3. **Marketing ROI**
- Measure efektivitas marketing channels
- Track UTM campaign performance
- Optimasi budget marketing

### 4. **Data-Driven Decisions**
- Campaign yang paling efektif
- Timing terbaik untuk campaign
- Target audience yang tepat

## 🔒 Keamanan & Privacy

### 1. **Data Protection**
- Tidak track data personal
- Hanya track anonymized data
- Compliance dengan GDPR/PPID

### 2. **Consent Management**
- Analytics hanya aktif jika user consent
- Opt-out option tersedia
- Transparent data collection

### 3. **Secure Implementation**
- HTTPS only untuk tracking
- No sensitive data in URLs
- Secure API endpoints

## 🚀 Best Practices

### 1. **UTM Naming Convention**
```
Source: facebook, google, email, instagram, twitter
Medium: cpc, social, email, organic, referral
Campaign: ramadan2024, qurban2024, bencana2024
```

### 2. **Event Naming**
```
campaign_view
donation_started
donation_completed
campaign_engagement
campaign_created
campaign_updated
```

### 3. **Data Consistency**
- Gunakan format yang konsisten
- Validate data sebelum tracking
- Handle errors gracefully

## 🐛 Troubleshooting

### Common Issues

1. **Analytics tidak track**
   - Check environment variables
   - Verify analytics service enabled
   - Check browser console untuk errors

2. **UTM parameters tidak muncul**
   - Verify database migration
   - Check form submission
   - Validate model fillable fields

3. **Events tidak terkirim**
   - Check network tab di browser
   - Verify analytics service
   - Check server logs

### Debug Commands

```bash
# Test analytics service
php artisan analytics:test

# Test campaign analytics
php artisan analytics:test-campaign

# Check analytics status
php artisan analytics:status
```

## 📚 Referensi

- [Google Analytics Events](https://developers.google.com/analytics/devguides/collection/gtagjs/events)
- [Facebook Pixel Events](https://developers.facebook.com/docs/facebook-pixel/implementation/conversion-tracking)
- [UTM Parameters Guide](https://support.google.com/analytics/answer/1033863)
- [Laravel Analytics Integration](https://laravel.com/docs/analytics)

---

**🎉 Fitur Campaign Analytics siap digunakan untuk tracking yang lebih detail dan actionable insights!** 