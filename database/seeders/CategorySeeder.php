<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Bantuan Sosial',
                'description' => 'Program bantuan untuk masyarakat yang membutuhkan',
                'color' => '#3B82F6',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Bencana Alam',
                'description' => 'Bantuan untuk korban bencana alam',
                'color' => '#EF4444',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Palestina',
                'description' => 'Bantuan kemanusiaan untuk Palestina',
                'color' => '#10B981',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Pendidikan',
                'description' => 'Program bantuan pendidikan dan beasiswa',
                'color' => '#8B5CF6',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Zakat',
                'description' => 'Program zakat dan sedekah',
                'color' => '#F59E0B',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Kesehatan',
                'description' => 'Bantuan kesehatan dan medis',
                'color' => '#EC4899',
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Masjid',
                'description' => 'Pembangunan dan renovasi masjid',
                'color' => '#06B6D4',
                'sort_order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Yatim Dhuafa',
                'description' => 'Bantuan untuk anak yatim dan dhuafa',
                'color' => '#84CC16',
                'sort_order' => 8,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
