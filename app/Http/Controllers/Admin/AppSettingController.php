<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AppSettingController extends Controller
{
    public function index()
    {
        $groups = [
            'general' => 'Pengaturan Umum',
            'contact' => 'Informasi Kontak',
            'social' => 'Media Sosial',
            'appearance' => 'Tampilan',
            'analytics' => 'Analytics & Tracking'
        ];

        $settings = [];
        foreach ($groups as $groupKey => $groupName) {
            $settings[$groupKey] = [
                'name' => $groupName,
                'settings' => AppSetting::getByGroup($groupKey)
            ];
        }

        return view('admin.settings.index', compact('settings', 'groups'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'nullable|string'
        ]);

        foreach ($request->settings as $key => $value) {
            $setting = AppSetting::where('key', $key)->first();
            if ($setting) {
                // Handle file upload for image type
                if ($setting->type === 'image' && $request->hasFile("settings.{$key}")) {
                    $file = $request->file("settings.{$key}");
                    if ($file->isValid()) {
                        // Delete old file if exists
                        if ($setting->value && Storage::disk('public')->exists($setting->value)) {
                            Storage::disk('public')->delete($setting->value);
                        }
                        
                        // Store new file
                        $path = $file->store('settings', 'public');
                        $value = $path;
                    }
                }
                
                // Validate color format for color type
                if ($setting->type === 'color' && $value) {
                    if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $value)) {
                        return redirect()->back()
                            ->withErrors(["settings.{$key}" => 'Format warna harus berupa hex color (contoh: #2563eb)'])
                            ->withInput();
                    }
                }
                
                // Handle toggle type
                if ($setting->type === 'toggle') {
                    $value = $value ? '1' : '0';
                }
                
                // Validate social media URLs
                if (str_starts_with($setting->key, 'social_') && $value && $setting->key !== 'social_telegram') {
                    if (!filter_var($value, FILTER_VALIDATE_URL)) {
                        return redirect()->back()
                            ->withErrors(["settings.{$key}" => 'URL harus valid (contoh: https://facebook.com/username)'])
                            ->withInput();
                    }
                }
                
                // Validate Telegram URL (can be t.me or telegram.me)
                if ($setting->key === 'social_telegram' && $value) {
                    if (!preg_match('/^(https?:\/\/)?(t\.me|telegram\.me)\/[a-zA-Z0-9_]{5,}$/', $value)) {
                        return redirect()->back()
                            ->withErrors(["settings.{$key}" => 'URL Telegram harus valid (contoh: https://t.me/username)'])
                            ->withInput();
                    }
                }
                
                // Format social media URLs to always have https
                if (str_starts_with($setting->key, 'social_') && $value) {
                    if (!str_starts_with($value, 'http')) {
                        $value = 'https://' . $value;
                    }
                }
                
                // Validate Google Analytics ID format
                if ($setting->key === 'google_analytics_id' && $value) {
                    if (!preg_match('/^G-[A-Z0-9]{10}$/', $value)) {
                        return redirect()->back()
                            ->withErrors(["settings.{$key}" => 'Google Analytics ID harus dalam format G-XXXXXXXXXX'])
                            ->withInput();
                    }
                }
                
                // Validate Facebook Pixel ID format
                if ($setting->key === 'facebook_pixel_id' && $value) {
                    if (!preg_match('/^[0-9]{10,15}$/', $value)) {
                        return redirect()->back()
                            ->withErrors(["settings.{$key}" => 'Facebook Pixel ID harus berupa angka 10-15 digit'])
                            ->withInput();
                    }
                }
                
                $setting->update(['value' => $value]);
            }
        }

        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan berhasil diperbarui!');
    }

    public function reset()
    {
        // Reset all settings to default values
        $this->call('db:seed', ['--class' => 'AppSettingSeeder']);
        
        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan berhasil direset ke nilai default!');
    }
}
