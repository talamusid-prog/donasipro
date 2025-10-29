<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert analytics settings
        DB::table('app_settings')->insert([
            [
                'key' => 'google_analytics_id',
                'value' => '',
                'type' => 'text',
                'label' => 'Google Analytics ID',
                'description' => 'Masukkan Google Analytics Measurement ID (format: G-XXXXXXXXXX)',
                'group' => 'analytics',
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'google_analytics_enabled',
                'value' => '0',
                'type' => 'toggle',
                'label' => 'Aktifkan Google Analytics',
                'description' => 'Aktifkan tracking Google Analytics',
                'group' => 'analytics',
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'facebook_pixel_id',
                'value' => '',
                'type' => 'text',
                'label' => 'Facebook Pixel ID',
                'description' => 'Masukkan Facebook Pixel ID (format: XXXXXXXXXX)',
                'group' => 'analytics',
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'facebook_pixel_enabled',
                'value' => '0',
                'type' => 'toggle',
                'label' => 'Aktifkan Facebook Pixel',
                'description' => 'Aktifkan tracking Facebook Pixel',
                'group' => 'analytics',
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'analytics_track_donations',
                'value' => '1',
                'type' => 'toggle',
                'label' => 'Track Donasi',
                'description' => 'Track event donasi untuk analytics',
                'group' => 'analytics',
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'analytics_track_campaign_views',
                'value' => '1',
                'type' => 'toggle',
                'label' => 'Track Campaign Views',
                'description' => 'Track view campaign untuk analytics',
                'group' => 'analytics',
                'sort_order' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('app_settings')->whereIn('key', [
            'google_analytics_id',
            'google_analytics_enabled',
            'facebook_pixel_id',
            'facebook_pixel_enabled',
            'analytics_track_donations',
            'analytics_track_campaign_views'
        ])->delete();
    }
};
