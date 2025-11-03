<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::withCount('donations')->latest()->paginate(10);
        return view('admin.campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        $sections = Campaign::getSections();
        return view('admin.campaigns.create', compact('categories', 'sections'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'category' => 'required|exists:categories,id',
            'sections' => 'required|array',
            'sections.*' => 'in:featured,new,other',
            'target_amount' => 'required|integer|min:1000',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,completed,expired',
            'is_verified' => 'boolean',
            'utm_source' => 'nullable|string|max:100',
            'utm_medium' => 'nullable|string|max:100',
            'utm_campaign' => 'nullable|string|max:100',
            'track_conversions' => 'boolean',
            'track_engagement' => 'boolean',
            'enhanced_ecommerce' => 'boolean',
        ]);

        $data = $request->except(['image', 'sections']);
        $data['category_id'] = $request->category;
        $data['is_verified'] = $request->has('is_verified');
        $data['sections'] = $request->sections;
        
        // Handle analytics fields
        $data['utm_source'] = $request->utm_source;
        $data['utm_medium'] = $request->utm_medium;
        $data['utm_campaign'] = $request->utm_campaign;
        $data['track_conversions'] = $request->has('track_conversions');
        $data['track_engagement'] = $request->has('track_engagement');
        $data['enhanced_ecommerce'] = $request->has('enhanced_ecommerce');

        // Handle image upload
        if ($request->hasFile('image')) {
            try {
                Log::debug('Mulai proses upload gambar', [
                    'request_file' => $request->file('image'),
                    'request_path' => $request->file('image')->getRealPath(),
                ]);
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                Log::debug('Nama file gambar', ['imageName' => $imageName]);
                // Simpan ke storage/app/public/campaigns (untuk database)
                $path = $image->storeAs('public/campaigns', $imageName);
                Log::debug('Path simpan storage', ['path' => $path]);
                // Copy ke public/storage/campaigns (untuk akses web)
                $publicPath = public_path('storage/campaigns/' . $imageName);
                Log::debug('Public path', ['publicPath' => $publicPath]);
                if (!file_exists(public_path('storage/campaigns'))) {
                    mkdir(public_path('storage/campaigns'), 0755, true);
                    Log::debug('Membuat folder public/storage/campaigns');
                }
                $copyResult = copy($image->getRealPath(), $publicPath);
                Log::debug('Hasil copy file', ['copyResult' => $copyResult]);
                $data['image_url'] = '/storage/campaigns/' . $imageName;
                Log::info('Upload gambar campaign', [
                    'original_name' => $image->getClientOriginalName(),
                    'path' => $path,
                    'url' => $data['image_url'],
                    'mode' => 'store',
                ]);
            } catch (\Exception $e) {
                Log::error('Campaign: Gagal upload image', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                return back()->withErrors(['image' => 'Gagal mengupload gambar: ' . $e->getMessage()]);
            }
        }

        $campaign = Campaign::create($data);
        
        // Track campaign creation event
        if ($campaign->track_conversions) {
            try {
                app(\App\Services\AnalyticsService::class)->trackEvent('campaign_created', [
                    'campaign_id' => $campaign->id,
                    'campaign_title' => $campaign->title,
                    'category' => $campaign->category?->name,
                    'target_amount' => $campaign->target_amount,
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to track campaign creation event', ['error' => $e->getMessage()]);
            }
        }
        
        Log::info('Campaign berhasil dibuat dengan analytics', [
            'campaign_id' => $campaign->id,
            'analytics_enabled' => $campaign->hasAnalyticsTracking(),
        ]);

        return redirect()->route('admin.campaigns.index')
                        ->with('success', 'Campaign berhasil dibuat!');
    }

    public function edit(Campaign $campaign)
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        $sections = Campaign::getSections();
        return view('admin.campaigns.edit', compact('campaign', 'categories', 'sections'));
    }

    public function update(Request $request, Campaign $campaign)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'category' => 'required|exists:categories,id',
            'sections' => 'required|array',
            'sections.*' => 'in:featured,new,other',
            'target_amount' => 'required|integer|min:1000',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,completed,expired',
            'is_verified' => 'boolean',
            'utm_source' => 'nullable|string|max:100',
            'utm_medium' => 'nullable|string|max:100',
            'utm_campaign' => 'nullable|string|max:100',
            'track_conversions' => 'boolean',
            'track_engagement' => 'boolean',
            'enhanced_ecommerce' => 'boolean',
        ]);

        $data = $request->except(['image', 'sections']);
        $data['category_id'] = $request->category;
        $data['is_verified'] = $request->has('is_verified');
        $data['sections'] = $request->sections;
        
        // Handle analytics fields
        $data['utm_source'] = $request->utm_source;
        $data['utm_medium'] = $request->utm_medium;
        $data['utm_campaign'] = $request->utm_campaign;
        $data['track_conversions'] = $request->has('track_conversions');
        $data['track_engagement'] = $request->has('track_engagement');
        $data['enhanced_ecommerce'] = $request->has('enhanced_ecommerce');

        // Handle image upload
        if ($request->hasFile('image')) {
            try {
                Log::debug('Mulai proses upload gambar (update)', [
                    'request_file' => $request->file('image'),
                    'request_path' => $request->file('image')->getRealPath(),
                ]);
                // Delete old image if exists
                if ($campaign->image_url && str_contains($campaign->image_url, 'storage/campaigns/')) {
                    $oldImageName = basename($campaign->image_url);
                    Storage::delete('public/campaigns/' . $oldImageName);
                    $publicPath = public_path('storage/campaigns/' . $oldImageName);
                    if (file_exists($publicPath)) {
                        unlink($publicPath);
                    }
                    Log::debug('Hapus gambar lama', ['oldImageName' => $oldImageName]);
                }
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                Log::debug('Nama file gambar (update)', ['imageName' => $imageName]);
                // Simpan ke storage/app/public/campaigns (untuk database)
                $path = $image->storeAs('public/campaigns', $imageName);
                Log::debug('Path simpan storage (update)', ['path' => $path]);
                // Copy ke public/storage/campaigns (untuk akses web)
                $publicPath = public_path('storage/campaigns/' . $imageName);
                Log::debug('Public path (update)', ['publicPath' => $publicPath]);
                if (!file_exists(public_path('storage/campaigns'))) {
                    mkdir(public_path('storage/campaigns'), 0755, true);
                    Log::debug('Membuat folder public/storage/campaigns (update)');
                }
                $copyResult = copy($image->getRealPath(), $publicPath);
                Log::debug('Hasil copy file (update)', ['copyResult' => $copyResult]);
                $data['image_url'] = '/storage/campaigns/' . $imageName;
                Log::info('Upload gambar campaign', [
                    'original_name' => $image->getClientOriginalName(),
                    'path' => $path,
                    'url' => $data['image_url'],
                    'mode' => 'update',
                ]);
            } catch (\Exception $e) {
                Log::error('Campaign: Gagal upload image (update)', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                return back()->withErrors(['image' => 'Gagal mengupload gambar: ' . $e->getMessage()]);
            }
        }

        $campaign->update($data);
        
        // Track campaign update event
        if ($campaign->track_conversions) {
            try {
                app(\App\Services\AnalyticsService::class)->trackEvent('campaign_updated', [
                    'campaign_id' => $campaign->id,
                    'campaign_title' => $campaign->title,
                    'category' => $campaign->category?->name,
                    'target_amount' => $campaign->target_amount,
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to track campaign update event', ['error' => $e->getMessage()]);
            }
        }
        
        Log::info('Campaign berhasil diperbarui dengan analytics', [
            'campaign_id' => $campaign->id,
            'analytics_enabled' => $campaign->hasAnalyticsTracking(),
        ]);

        return redirect()->route('admin.campaigns.index')
                        ->with('success', 'Campaign berhasil diperbarui!');
    }

    public function destroy(Campaign $campaign)
    {
        // Delete image if exists
        if ($campaign->image_url && str_contains($campaign->image_url, 'storage/campaigns/')) {
            $imageName = basename($campaign->image_url);
            Storage::delete('public/campaigns/' . $imageName);
            // Hapus juga dari public/storage/campaigns
            $publicPath = public_path('storage/campaigns/' . $imageName);
            if (file_exists($publicPath)) {
                unlink($publicPath);
            }
        }

        $campaign->delete();

        return redirect()->route('admin.campaigns.index')
                        ->with('success', 'Campaign berhasil dihapus!');
    }

    public function show(Campaign $campaign)
    {
        $campaign->load('donations');
        return view('admin.campaigns.show', compact('campaign'));
    }

    public function uploadImage(Request $request)
    {
        try {
            $request->validate([
                'upload' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
            ]);

            $image = $request->file('upload');
            $imageName = time() . '_' . $image->getClientOriginalName();
            
            // Simpan ke storage/app/public/editor-images
            $path = $image->storeAs('public/editor-images', $imageName);
            
            // Copy ke public/storage/editor-images (untuk akses web)
            $publicPath = public_path('storage/editor-images/' . $imageName);
            if (!file_exists(public_path('storage/editor-images'))) {
                mkdir(public_path('storage/editor-images'), 0755, true);
            }
            copy($image->getRealPath(), $publicPath);
            
            // Gunakan relative URL untuk menghindari mixed content
            $imageUrl = '/storage/editor-images/' . $imageName;
            
            // Response untuk CKEditor
            return response()->json([
                'url' => $imageUrl,
                'uploaded' => 1,
                'fileName' => $imageName
            ]);
            
        } catch (\Exception $e) {
            Log::error('CKEditor: Gagal upload image', [
                'error' => $e->getMessage(), 
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'uploaded' => 0,
                'error' => [
                    'message' => 'Gagal mengupload gambar: ' . $e->getMessage()
                ]
            ]);
        }
    }
} 