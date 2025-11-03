<?php

namespace Database\Seeders;

use App\Models\Slider;
use Illuminate\Database\Seeder;

class SliderSeeder extends Seeder
{
    public function run(): void
    {
        $sliders = [
            [
                'title' => 'Bantu Sesama',
                'image' => 'https://images.unsplash.com/photo-1532629345422-7515f3d16bb6?w=800&h=400&fit=crop',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Peduli Pendidikan',
                'image' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9e1?w=800&h=400&fit=crop',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Bencana Alam',
                'image' => 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=800&h=400&fit=crop',
                'order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($sliders as $slider) {
            Slider::create($slider);
        }
    }
} 