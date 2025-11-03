<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TripayChannel;
use App\Services\TripayService;
use Illuminate\Http\Request;

class TripayChannelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $channels = TripayChannel::orderBy('group')->orderBy('name')->get();
        $groups = $channels->groupBy('group');
        
        // Get Tripay settings from database
        $tripaySettings = \App\Models\AppSetting::where('key', 'like', 'tripay_%')
            ->pluck('value', 'key')
            ->mapWithKeys(function ($value, $key) {
                return [str_replace('tripay_', '', $key) => $value];
            });
        
        return view('admin.tripay-channels.index', compact('channels', 'groups', 'tripaySettings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TripayChannel $channel)
    {
        $request->validate([
            'is_enabled' => 'boolean',
            'minimum_amount' => 'integer|min:0',
            'maximum_amount' => 'integer|min:0',
        ]);

        $channel->update($request->only(['is_enabled', 'minimum_amount', 'maximum_amount']));

        return redirect()->route('admin.tripay-channels.index')
                        ->with('success', "Channel {$channel->name} berhasil diperbarui");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function sync()
    {
        $tripay = new TripayService();
        $result = $tripay->syncChannels();

        if ($result['success']) {
            return redirect()->route('admin.tripay-channels.index')
                           ->with('success', $result['message']);
        } else {
            return redirect()->route('admin.tripay-channels.index')
                           ->with('error', $result['message']);
        }
    }

    public function toggle(Request $request, TripayChannel $channel)
    {
        $channel->update([
            'is_enabled' => !$channel->is_enabled
        ]);

        $status = $channel->is_enabled ? 'diaktifkan' : 'dinonaktifkan';
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Channel {$channel->name} berhasil {$status}",
                'is_enabled' => $channel->is_enabled
            ]);
        }
        
        return redirect()->route('admin.tripay-channels.index')
                        ->with('success', "Channel {$channel->name} berhasil {$status}");
    }

    public function bulkToggle(Request $request)
    {
        $request->validate([
            'action' => 'required|in:enable,disable',
            'channels' => 'required',
        ]);

        // Handle both JSON string and array
        $channels = $request->channels;
        if (is_string($channels)) {
            $channels = json_decode($channels, true);
        }

        if (!is_array($channels) || empty($channels)) {
            return redirect()->route('admin.tripay-channels.index')
                            ->with('error', 'Tidak ada channel yang dipilih');
        }

        $isEnabled = $request->action === 'enable';
        
        TripayChannel::whereIn('id', $channels)
                    ->update(['is_enabled' => $isEnabled]);

        $action = $isEnabled ? 'diaktifkan' : 'dinonaktifkan';
        
        return redirect()->route('admin.tripay-channels.index')
                        ->with('success', count($channels) . " channel berhasil {$action}");
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'api_key' => 'required|string',
            'private_key' => 'required|string',
            'merchant_code' => 'required|string',
            'environment' => 'required|in:sandbox,production',
            'base_url' => 'required|url',
            'is_production' => 'boolean',
        ]);

        // Update settings in database or config
        $settings = [
            'api_key' => $request->api_key,
            'private_key' => $request->private_key,
            'merchant_code' => $request->merchant_code,
            'environment' => $request->environment,
            'base_url' => $request->base_url,
            'is_production' => $request->has('is_production') ? '1' : '0',
        ];

        // Store in app_settings table
        foreach ($settings as $key => $value) {
            \App\Models\AppSetting::updateOrCreate(
                ['key' => 'tripay_' . $key],
                ['value' => $value]
            );
        }

        return redirect()->route('admin.tripay-channels.index')
                        ->with('success', 'Pengaturan Tripay berhasil diperbarui');
    }

    public function testConnection(Request $request)
    {
        try {
            // Get current settings
            $settings = \App\Models\AppSetting::where('key', 'like', 'tripay_%')
                ->pluck('value', 'key')
                ->mapWithKeys(function ($value, $key) {
                    return [str_replace('tripay_', '', $key) => $value];
                });

            // Create temporary TripayService with current settings
            $tripay = new \App\Services\TripayService();
            
            // Override settings if provided
            if ($settings->count() > 0) {
                $tripay->setConfig([
                    'api_key' => $settings->get('api_key', ''),
                    'private_key' => $settings->get('private_key', ''),
                    'merchant_code' => $settings->get('merchant_code', ''),
                    'base_url' => $settings->get('base_url', 'https://tripay.co.id/api-sandbox'),
                    'is_production' => filter_var($settings->get('is_production', false), FILTER_VALIDATE_BOOLEAN),
                ]);
            }

            $result = $tripay->testConnection();

            if ($request->expectsJson()) {
                return response()->json($result);
            }

            return redirect()->route('admin.tripay-channels.index')
                            ->with($result['success'] ? 'success' : 'error', $result['message']);

        } catch (\Exception $e) {
            $errorMessage = 'Error: ' . $e->getMessage();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ]);
            }

            return redirect()->route('admin.tripay-channels.index')
                            ->with('error', $errorMessage);
        }
    }
}
