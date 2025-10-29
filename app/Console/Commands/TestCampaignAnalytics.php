<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Campaign;
use App\Services\AnalyticsService;

class TestCampaignAnalytics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analytics:test-campaign {campaign_id?} {--event=view}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test analytics tracking untuk campaign';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $campaignId = $this->argument('campaign_id');
        $event = $this->option('event');
        
        $this->info('🔍 Testing Campaign Analytics...');
        $this->newLine();
        
        // Get campaign
        if ($campaignId) {
            $campaign = Campaign::find($campaignId);
            if (!$campaign) {
                $this->error("❌ Campaign dengan ID {$campaignId} tidak ditemukan!");
                return 1;
            }
        } else {
            $campaign = Campaign::first();
            if (!$campaign) {
                $this->error("❌ Tidak ada campaign di database!");
                return 1;
            }
        }
        
        $this->info("📊 Campaign: {$campaign->title}");
        $this->info("🆔 ID: {$campaign->id}");
        $this->info("📈 Progress: {$campaign->progress_percentage}%");
        $this->newLine();
        
        // Check analytics settings
        $this->info('⚙️  Analytics Settings:');
        $this->table(
            ['Setting', 'Value'],
            [
                ['Track Conversions', $campaign->track_conversions ? '✅ Yes' : '❌ No'],
                ['Track Engagement', $campaign->track_engagement ? '✅ Yes' : '❌ No'],
                ['Enhanced E-commerce', $campaign->enhanced_ecommerce ? '✅ Yes' : '❌ No'],
                ['UTM Source', $campaign->utm_source ?: 'Not set'],
                ['UTM Medium', $campaign->utm_medium ?: 'Not set'],
                ['UTM Campaign', $campaign->utm_campaign ?: 'Not set'],
            ]
        );
        $this->newLine();
        
        // Get analytics service
        $analyticsService = app(AnalyticsService::class);
        
        // Check global analytics status
        $this->info('🌐 Global Analytics Status:');
        $this->table(
            ['Service', 'Status'],
            [
                ['Google Analytics', $analyticsService->isGoogleAnalyticsEnabled() ? '✅ Active' : '❌ Inactive'],
                ['Facebook Pixel', $analyticsService->isFacebookPixelEnabled() ? '✅ Active' : '❌ Inactive'],
            ]
        );
        $this->newLine();
        
        // Test tracking events
        $this->info("🎯 Testing Event: {$event}");
        
        $analyticsData = $campaign->getAnalyticsData();
        
        try {
            switch ($event) {
                case 'view':
                    $analyticsService->trackEvent('campaign_view', $analyticsData);
                    $this->info('✅ Campaign view event tracked successfully');
                    break;
                    
                case 'donation':
                    $analyticsService->trackEvent('donation_started', $analyticsData);
                    $this->info('✅ Donation started event tracked successfully');
                    break;
                    
                case 'conversion':
                    $analyticsService->trackEvent('donation_completed', $analyticsData);
                    $this->info('✅ Donation completed event tracked successfully');
                    break;
                    
                case 'engagement':
                    $analyticsService->trackEvent('campaign_engagement', $analyticsData);
                    $this->info('✅ Campaign engagement event tracked successfully');
                    break;
                    
                default:
                    $analyticsService->trackEvent($event, $analyticsData);
                    $this->info("✅ Custom event '{$event}' tracked successfully");
            }
            
            $this->newLine();
            $this->info('📋 Analytics Data Sent:');
            $this->table(
                ['Key', 'Value'],
                collect($analyticsData)->map(function ($value, $key) {
                    return [$key, is_bool($value) ? ($value ? 'Yes' : 'No') : $value];
                })->toArray()
            );
            
        } catch (\Exception $e) {
            $this->error("❌ Failed to track event: {$e->getMessage()}");
            return 1;
        }
        
        $this->newLine();
        $this->info('🔗 Campaign Tracking URL:');
        $this->line($campaign->getTrackingUrl());
        
        $this->newLine();
        $this->info('✅ Campaign analytics test completed successfully!');
        
        return 0;
    }
}
