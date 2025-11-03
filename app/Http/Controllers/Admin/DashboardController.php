<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Donation;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistik Utama
        $totalDonations = Donation::where('payment_status', 'success')->sum('amount');
        $successfulDonationsCount = Donation::where('payment_status', 'success')->count();
        $totalCampaigns = Campaign::count();
        $totalUsers = User::count();

        // Donasi Terbaru
        $recentDonations = Donation::with('campaign', 'user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        // Data untuk Grafik Donasi 7 Hari Terakhir
        $startDate = Carbon::now()->subDays(6)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        $donationsData = Donation::where('payment_status', 'success')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get([
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(amount) as total')
            ])
            ->keyBy('date');

        $dates = [];
        $totals = [];
        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $formattedDate = $date->format('Y-m-d');
            $dates[] = $date->format('d M');
            $totals[] = $donationsData->has($formattedDate) ? $donationsData[$formattedDate]->total : 0;
        }

        $chartData = [
            'labels' => $dates,
            'totals' => $totals,
        ];
            
        return view('admin.dashboard', compact(
            'totalDonations',
            'successfulDonationsCount',
            'totalCampaigns',
            'totalUsers',
            'recentDonations',
            'chartData'
        ));
    }
}
