<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Category;
use App\Models\Donation;
use App\Models\BankAccount;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        $query = Campaign::where('status', 'active');
        
        // Filter berdasarkan kategori
        if ($request->category && $request->category !== 'all') {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }
        
        $campaigns = $query->latest()->paginate(12);
        
        // Ambil semua kategori untuk filter
        $categories = Category::active()->ordered()->get();
        
        return view('campaigns.index', compact('campaigns', 'categories'));
    }

    public function show(Campaign $campaign, Request $request)
    {
        $donations = $campaign->donations()->latest()->get();
        $showAllDonors = $request->has('show_all_donors');
        $showAllPrayers = $request->has('show_all_prayers');
        
        // Track campaign view for analytics
        try {
            $analyticsService = new \App\Services\AnalyticsService();
            $analyticsService->trackCampaignView($campaign);
        } catch (\Exception $e) {
            \Log::error('Failed to track campaign view for campaign #' . $campaign->id . ': ' . $e->getMessage());
        }
        
        return view('campaigns.show', compact('campaign', 'donations', 'showAllDonors', 'showAllPrayers'));
    }

    public function donate(Campaign $campaign)
    {
        // Ambil data bank account yang aktif
        $bankAccounts = BankAccount::active()->orderBy('bank_name')->get();
        
        // Ambil channel Tripay yang diaktifkan
        $tripayService = new \App\Services\TripayService();
        $tripayChannels = $tripayService->getEnabledPaymentMethods();
        
        return view('campaigns.donate', compact('campaign', 'bankAccounts', 'tripayChannels'));
    }
} 