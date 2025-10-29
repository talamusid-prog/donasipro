<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AppSetting;

class AppSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            [
                'key' => 'app_name',
                'value' => 'Donasi Apps',
                'type' => 'text',
                'label' => 'Nama Aplikasi',
                'description' => 'Nama aplikasi yang akan ditampilkan di berbagai tempat',
                'group' => 'general',
                'sort_order' => 1
            ],
            [
                'key' => 'organization_name',
                'value' => 'Yayasan Peduli Anak',
                'type' => 'text',
                'label' => 'Nama Organisasi/Yayasan',
                'description' => 'Nama organisasi atau yayasan yang mengelola aplikasi',
                'group' => 'general',
                'sort_order' => 2
            ],
            [
                'key' => 'app_description',
                'value' => 'Platform donasi online untuk membantu sesama',
                'type' => 'textarea',
                'label' => 'Deskripsi Aplikasi',
                'description' => 'Deskripsi singkat tentang aplikasi',
                'group' => 'general',
                'sort_order' => 3
            ],
            [
                'key' => 'app_logo',
                'value' => null,
                'type' => 'image',
                'label' => 'Logo Aplikasi',
                'description' => 'Logo yang akan ditampilkan di aplikasi',
                'group' => 'general',
                'sort_order' => 4
            ],

            // Contact Settings
            [
                'key' => 'contact_email',
                'value' => 'info@donasiapps.com',
                'type' => 'email',
                'label' => 'Email Kontak',
                'description' => 'Email untuk kontak umum',
                'group' => 'contact',
                'sort_order' => 1
            ],
            [
                'key' => 'contact_phone',
                'value' => '+62 812-3456-7890',
                'type' => 'phone',
                'label' => 'Nomor Telepon',
                'description' => 'Nomor telepon untuk kontak',
                'group' => 'contact',
                'sort_order' => 2
            ],
            [
                'key' => 'contact_address',
                'value' => 'Jl. Contoh No. 123, Jakarta Selatan, DKI Jakarta 12345',
                'type' => 'textarea',
                'label' => 'Alamat',
                'description' => 'Alamat lengkap organisasi',
                'group' => 'contact',
                'sort_order' => 3
            ],
            [
                'key' => 'contact_whatsapp',
                'value' => '+62 812-3456-7890',
                'type' => 'phone',
                'label' => 'WhatsApp',
                'description' => 'Nomor WhatsApp untuk kontak',
                'group' => 'contact',
                'sort_order' => 4
            ],

            // Social Media Settings
            [
                'key' => 'social_facebook',
                'value' => 'https://facebook.com/donasiapps',
                'type' => 'text',
                'label' => 'Facebook',
                'description' => 'Link Facebook organisasi',
                'group' => 'social',
                'sort_order' => 1
            ],
            [
                'key' => 'social_instagram',
                'value' => 'https://instagram.com/donasiapps',
                'type' => 'text',
                'label' => 'Instagram',
                'description' => 'Link Instagram organisasi',
                'group' => 'social',
                'sort_order' => 2
            ],
            [
                'key' => 'social_twitter',
                'value' => 'https://twitter.com/donasiapps',
                'type' => 'text',
                'label' => 'Twitter',
                'description' => 'Link Twitter organisasi',
                'group' => 'social',
                'sort_order' => 3
            ],
            [
                'key' => 'social_youtube',
                'value' => 'https://youtube.com/donasiapps',
                'type' => 'text',
                'label' => 'YouTube',
                'description' => 'Link YouTube organisasi',
                'group' => 'social',
                'sort_order' => 4
            ],
            [
                'key' => 'social_linkedin',
                'value' => 'https://linkedin.com/company/donasiapps',
                'type' => 'text',
                'label' => 'LinkedIn',
                'description' => 'Link LinkedIn organisasi',
                'group' => 'social',
                'sort_order' => 5
            ],
            [
                'key' => 'social_telegram',
                'value' => 'https://t.me/donasiapps',
                'type' => 'text',
                'label' => 'Telegram',
                'description' => 'Link Telegram organisasi',
                'group' => 'social',
                'sort_order' => 6
            ],
            [
                'key' => 'show_social_media',
                'value' => '1',
                'type' => 'toggle',
                'label' => 'Tampilkan Social Media',
                'description' => 'Pilih untuk menampilkan atau menyembunyikan social media links di halaman utama',
                'group' => 'social',
                'sort_order' => 7
            ],

            // Appearance Settings
            [
                'key' => 'primary_color',
                'value' => '#2563eb',
                'type' => 'color',
                'label' => 'Warna Utama',
                'description' => 'Warna utama aplikasi (hex code)',
                'group' => 'appearance',
                'sort_order' => 1
            ],
            [
                'key' => 'footer_text',
                'value' => '© 2024 Donasi Apps. Semua hak dilindungi.',
                'type' => 'text',
                'label' => 'Teks Footer',
                'description' => 'Teks yang ditampilkan di footer',
                'group' => 'appearance',
                'sort_order' => 2
            ],
            [
                'key' => 'meta_description',
                'value' => 'Platform donasi online terpercaya untuk membantu sesama',
                'type' => 'textarea',
                'label' => 'Meta Description',
                'description' => 'Deskripsi untuk SEO',
                'group' => 'appearance',
                'sort_order' => 3
            ],
            [
                'key' => 'meta_keywords',
                'value' => 'donasi, charity, bantuan, yayasan, peduli',
                'type' => 'text',
                'label' => 'Meta Keywords',
                'description' => 'Kata kunci untuk SEO',
                'group' => 'appearance',
                'sort_order' => 4
            ]
        ];

        foreach ($settings as $setting) {
            AppSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
