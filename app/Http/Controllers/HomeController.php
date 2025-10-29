<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Category;
use App\Models\Slider;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Campaign::where('status', 'active');
        
        if ($request->category && $request->category !== 'all') {
            $query->where('category', $request->category);
        }
        
        $campaigns = $query->latest()->get();
        
        // Ambil campaign unggulan dari database
        $featuredCampaigns = Campaign::where('status', 'active')
            ->whereJsonContains('sections', 'featured')
            ->where('is_verified', true)
            ->with('category')
            ->latest()
            ->take(5)
            ->get();
        
        // Ambil campaign terbaru dari database
        $latestCampaigns = Campaign::where('status', 'active')
            ->where('is_verified', true)
            ->with('category')
            ->withCount('donations')
            ->latest()
            ->take(4)
            ->get();
        
        // Ambil campaign program lainnya dari database
        $otherCampaigns = Campaign::where('status', 'active')
            ->whereJsonContains('sections', 'other')
            ->where('is_verified', true)
            ->with('category')
            ->withCount('donations')
            ->latest()
            ->take(5)
            ->get();
        
        // Ambil kategori aktif dari database
        $categories = Category::active()->ordered()->get();
        $sliders = Slider::active()->ordered()->get();
        \Log::info('DEBUG - Featured Campaigns', [
            'featured' => $featuredCampaigns->map(function($c) { return ['id' => $c->id, 'title' => $c->title, 'image_url' => $c->image_url]; })
        ]);

        // Ambil doa-doa terbaik
        $bestPrayers = $this->getBestPrayers();

        return view('home', compact('campaigns', 'categories', 'sliders', 'featuredCampaigns', 'latestCampaigns', 'otherCampaigns', 'bestPrayers'));
    }

    private function getBestPrayers()
    {
        // Ambil doa-doa terbaik dari database
        $prayers = Donation::whereNotNull('message')
            ->where('message', '!=', '')
            ->where('payment_status', 'success')
            ->where('is_anonymous', false)
            ->where(function($query) {
                $query->where('message', 'like', '%Allah%')
                      ->orWhere('message', 'like', '%aamiin%')
                      ->orWhere('message', 'like', '%semoga%')
                      ->orWhere('message', 'like', '%doa%')
                      ->orWhere('message', 'like', '%berkah%')
                      ->orWhere('message', 'like', '%pahala%');
            })
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function($item) {
                return [
                    'donor_name' => $item->donor_name,
                    'message' => $item->message,
                    'created_at' => $item->created_at,
                ];
            })
            ->toArray();

        // Doa default
        $defaultPrayers = [
            [
                'donor_name' => 'Siti Aminah',
                'message' => 'Semoga Allah SWT membalas kebaikan semua donatur dengan berlipat ganda. Aamiin.',
                'created_at' => now()->subDays(2)
            ],
            [
                'donor_name' => 'Budi Santoso',
                'message' => 'Semoga bantuan ini bisa meringankan beban mereka yang membutuhkan. Semoga Allah SWT selalu melindungi kita semua.',
                'created_at' => now()->subDays(5)
            ],
            [
                'donor_name' => 'Rina Wijaya',
                'message' => 'Doa terbaik untuk semua yang terlibat dalam kebaikan ini. Semoga menjadi amal jariyah yang terus mengalir.',
                'created_at' => now()->subDays(7)
            ],
            [
                'donor_name' => 'Andi Pratama',
                'message' => 'Semoga Allah SWT memberikan keberkahan pada setiap rupiah yang didonasikan. Aamiin ya rabbal alamin.',
                'created_at' => now()->subDays(10)
            ],
            [
                'donor_name' => 'Dewi Lestari',
                'message' => 'Doa untuk semua yang membutuhkan, semoga Allah SWT memberikan kemudahan dan pertolongan.',
                'created_at' => now()->subDays(12)
            ],
            [
                'donor_name' => 'Ahmad Hidayat',
                'message' => 'Semoga Allah SWT menerima amal ibadah kita semua dan memberikan pahala yang berlipat ganda.',
                'created_at' => now()->subDays(15)
            ],
            [
                'donor_name' => 'Nurul Hidayah',
                'message' => 'Doa untuk semua yang terlibat dalam kebaikan ini. Semoga Allah SWT selalu memberikan kemudahan.',
                'created_at' => now()->subDays(18)
            ],
            [
                'donor_name' => 'Muhammad Rizki',
                'message' => 'Semoga setiap rupiah yang didonasikan menjadi amal jariyah yang terus mengalir pahalanya.',
                'created_at' => now()->subDays(20)
            ]
        ];

        // Gabungkan, lalu ambil 5 teratas
        $allPrayers = array_slice(array_merge($prayers, $defaultPrayers), 0, 5);

        // Kembalikan sebagai collection agar mudah di-blade
        return collect($allPrayers);
    }
} 