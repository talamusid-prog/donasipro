# 📊 Analytics Integration Guide

## Overview

Aplikasi donasi ini telah dilengkapi dengan integrasi **Google Analytics** dan **Facebook Pixel** untuk tracking dan analisis performa. Fitur ini memungkinkan admin untuk memantau traffic, konversi donasi, dan perilaku pengguna.

## 🎯 Fitur yang Tersedia

### 1. Google Analytics Integration
- ✅ **Page Views Tracking** - Track semua halaman yang dikunjungi
- ✅ **Event Tracking** - Track event donasi dan campaign views
- ✅ **E-commerce Tracking** - Track nilai donasi dan konversi
- ✅ **User Behavior** - Track perilaku pengguna di website

### 2. Facebook Pixel Integration
- ✅ **Page Views** - Track halaman yang dikunjungi
- ✅ **Custom Events** - Track event donasi dan campaign
- ✅ **Conversion Tracking** - Track konversi donasi
- ✅ **Audience Building** - Build custom audience untuk ads

### 3. Event Tracking
- ✅ **Donation Completed** - Track setiap donasi berhasil
- ✅ **Campaign View** - Track view campaign
- ✅ **Payment Method** - Track metode pembayaran yang dipilih
- ✅ **User Type** - Track donor anonymous vs registered

## ⚙️ Setup & Konfigurasi

### 1. Akses Admin Panel
1. Login ke admin panel: `/admin/login`
2. Buka menu **Settings** → **Analytics & Tracking**

### 2. Google Analytics Setup
1. **Dapatkan Google Analytics ID:**
   - Buka [Google Analytics](https://analytics.google.com/)
   - Buat property baru atau gunakan yang ada
   - Copy **Measurement ID** (format: G-XXXXXXXXXX)

2. **Konfigurasi di Admin Panel:**
   - Aktifkan **Google Analytics**
   - Masukkan **Google Analytics ID**
   - Simpan pengaturan

### 3. Facebook Pixel Setup
1. **Dapatkan Facebook Pixel ID:**
   - Buka [Facebook Business Manager](https://business.facebook.com/)
   - Buat Pixel baru atau gunakan yang ada
   - Copy **Pixel ID** (format: 10-15 digit angka)

2. **Konfigurasi di Admin Panel:**
   - Aktifkan **Facebook Pixel**
   - Masukkan **Facebook Pixel ID**
   - Simpan pengaturan

## 📈 Event yang Di-Track

### 1. Donation Events
```javascript
// Event: donation_completed
{
  event: 'donation_completed',
  donation_id: 123,
  amount: 100000,
  campaign_id: 5,
  campaign_title: 'Pembangunan Masjid',
  payment_method: 'manual_bri',
  donor_type: 'registered' // atau 'anonymous'
}
```

### 2. Campaign Events
```javascript
// Event: campaign_view
{
  event: 'campaign_view',
  campaign_id: 5,
  campaign_title: 'Pembangunan Masjid',
  category: 'Masjid',
  target_amount: 50000000,
  current_amount: 25000000
}
```

## 🔧 Testing & Debugging

### 1. Command Line Testing
```bash
# Test analytics service
php artisan analytics:test

# Test dengan donation ID tertentu
php artisan analytics:test --donation-id=31

# Test dengan campaign ID tertentu
php artisan analytics:test --campaign-id=5
```

### 2. Browser Testing
1. **Google Analytics:**
   - Install [Google Analytics Debugger](https://chrome.google.com/webstore/detail/google-analytics-debugger/jnkmfdileelhofjcijamephohjechhna)
   - Buka browser console
   - Cek event yang dikirim

2. **Facebook Pixel:**
   - Install [Facebook Pixel Helper](https://chrome.google.com/webstore/detail/facebook-pixel-helper/fdgfkebogiimcoedmjlcbhdnplpnnmfn)
   - Buka browser console
   - Cek pixel events

### 3. Log Monitoring
```bash
# Cek log analytics events
tail -f storage/logs/laravel.log | grep "Analytics Event"
```

## 📊 Dashboard & Reports

### 1. Google Analytics Dashboard
- **Real-time Reports** - Lihat traffic real-time
- **Audience Reports** - Analisis demografi pengguna
- **Acquisition Reports** - Sumber traffic
- **Behavior Reports** - Perilaku pengguna
- **Conversion Reports** - Konversi donasi

### 2. Facebook Analytics
- **Events Manager** - Monitor semua events
- **Custom Conversions** - Track konversi custom
- **Audience Insights** - Analisis audience
- **Ad Performance** - Performa iklan

## 🎯 Best Practices

### 1. Privacy & Compliance
- ✅ **GDPR Compliance** - Implement consent management
- ✅ **Cookie Notice** - Tampilkan cookie banner
- ✅ **Data Retention** - Set retention policy
- ✅ **User Consent** - Dapatkan consent sebelum tracking

### 2. Performance Optimization
- ✅ **Async Loading** - Script dimuat secara async
- ✅ **Minimal Impact** - Tidak mempengaruhi page speed
- ✅ **Error Handling** - Graceful fallback jika gagal
- ✅ **Caching** - Cache analytics data

### 3. Data Quality
- ✅ **Event Validation** - Validasi data sebelum kirim
- ✅ **Duplicate Prevention** - Hindari event duplikat
- ✅ **Data Consistency** - Konsistensi format data
- ✅ **Error Logging** - Log error untuk debugging

## 🔍 Troubleshooting

### 1. Google Analytics Tidak Muncul
```bash
# Cek settings
php artisan analytics:test

# Cek log errors
tail -f storage/logs/laravel.log | grep "Analytics"
```

**Solusi:**
- Pastikan GA ID format benar (G-XXXXXXXXXX)
- Cek apakah GA diaktifkan di admin panel
- Pastikan tidak ada ad blocker yang aktif

### 2. Facebook Pixel Tidak Track
```bash
# Test pixel events
php artisan analytics:test --donation-id=31
```

**Solusi:**
- Pastikan Pixel ID format benar (10-15 digit)
- Cek apakah Pixel diaktifkan di admin panel
- Install Facebook Pixel Helper untuk debugging

### 3. Events Tidak Terkirim
```bash
# Cek pending events
php artisan analytics:test
```

**Solusi:**
- Cek session storage
- Pastikan tracking diaktifkan
- Cek error log untuk detail

## 📱 Mobile App Integration

### 1. React Native / Flutter
```javascript
// Track donation event
analytics.track('donation_completed', {
  donation_id: 123,
  amount: 100000,
  campaign_title: 'Pembangunan Masjid'
});
```

### 2. API Integration
```bash
# Track event via API
curl -X POST /api/analytics/track \
  -H "Content-Type: application/json" \
  -d '{
    "event": "donation_completed",
    "donation_id": 123,
    "amount": 100000
  }'
```

## 🔐 Security Considerations

### 1. Data Protection
- ✅ **Encryption** - Data dienkripsi saat transit
- ✅ **Access Control** - Hanya admin yang bisa akses settings
- ✅ **Audit Log** - Log semua perubahan settings
- ✅ **Backup** - Backup analytics data

### 2. Privacy Settings
- ✅ **IP Anonymization** - Anonymize IP addresses
- ✅ **Do Not Track** - Respect DNT headers
- ✅ **Consent Management** - User consent required
- ✅ **Data Retention** - Automatic data deletion

## 📈 Advanced Features

### 1. Custom Events
```php
// Track custom event
$analyticsService = new AnalyticsService();
$analyticsService->trackEvent([
    'event' => 'user_registration',
    'user_id' => $user->id,
    'registration_method' => 'email'
]);
```

### 2. Enhanced E-commerce
```php
// Track enhanced e-commerce
$analyticsService->trackEvent([
    'event' => 'purchase',
    'transaction_id' => $donation->id,
    'value' => $donation->amount,
    'currency' => 'IDR',
    'items' => [
        [
            'id' => $campaign->id,
            'name' => $campaign->title,
            'category' => $campaign->category->name,
            'price' => $donation->amount,
            'quantity' => 1
        ]
    ]
]);
```

## 🚀 Future Enhancements

### 1. Planned Features
- 🔄 **Real-time Dashboard** - Live analytics dashboard
- 🔄 **A/B Testing** - Campaign A/B testing
- 🔄 **Predictive Analytics** - ML-based predictions
- 🔄 **Advanced Segmentation** - User segmentation

### 2. Integration Roadmap
- 🔄 **Google Tag Manager** - GTM integration
- 🔄 **Hotjar** - User behavior tracking
- 🔄 **Mixpanel** - Advanced analytics
- 🔄 **Amplitude** - Product analytics

---

## 📞 Support

Jika mengalami masalah dengan analytics integration:

1. **Cek Dokumentasi** - Baca guide ini dengan teliti
2. **Test Command** - Jalankan `php artisan analytics:test`
3. **Cek Logs** - Monitor error logs
4. **Contact Support** - Hubungi tim development

---

**Versi:** 1.0.0  
**Update Terakhir:** Juli 2025  
**Compatibility:** Laravel 10+, PHP 8.1+ 