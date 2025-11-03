<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Campaign;
use App\Models\Category;
use Illuminate\Support\Str;

class ZakatCampaignSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil kategori Zakat
        $zakatCategory = Category::where('name', 'Zakat')->first();
        
        if (!$zakatCategory) {
            // Buat kategori Zakat jika belum ada
            $zakatCategory = Category::create([
                'name' => 'Zakat',
                'description' => 'Program zakat dan sedekah',
                'color' => '#F59E0B',
                'sort_order' => 5,
                'is_active' => true,
            ]);
        }

        // Buat campaign Zakat Mal
        Campaign::create([
            'title' => 'Zakat Mal',
            'slug' => 'zakat-mal',
            'description' => 'Program pengumpulan zakat mal untuk disalurkan kepada mustahik yang berhak menerima zakat. Zakat mal dikenakan atas harta yang dimiliki seperti emas, perak, uang, dan harta lainnya yang telah mencapai nishab.',
            'image_url' => '/images/placeholder.svg',
            'category_id' => $zakatCategory->id,
            'sections' => ['other'],
            'target_amount' => 1000000000,
            'current_amount' => 0,
            'start_date' => now(),
            'end_date' => now()->addYear(),
            'status' => 'active',
            'is_verified' => true,
        ]);

        // Buat campaign Zakat Penghasilan
        Campaign::create([
            'title' => 'Zakat Penghasilan',
            'slug' => 'zakat-penghasilan',
            'description' => 'Program pengumpulan zakat penghasilan untuk disalurkan kepada mustahik yang berhak menerima zakat. Zakat penghasilan dikenakan atas penghasilan dari pekerjaan, profesi, atau usaha yang telah mencapai nishab.',
            'image_url' => '/images/placeholder.svg',
            'category_id' => $zakatCategory->id,
            'sections' => ['other'],
            'target_amount' => 1000000000,
            'current_amount' => 0,
            'start_date' => now(),
            'end_date' => now()->addYear(),
            'status' => 'active',
            'is_verified' => true,
        ]);
    }
} 