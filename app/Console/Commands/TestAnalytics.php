<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AnalyticsService;
use App\Models\Donation;
use App\Models\Campaign;

class TestAnalytics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analytics:test {--donation-id=} {--campaign-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test analytics service functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $analyticsService = new AnalyticsService();

        $this->info('=== Analytics Service Test ===');
        $this->newLine();

        // Test settings
        $this->info('1. Settings Check:');
        $this->line('   Google Analytics Enabled: ' . ($analyticsService->isGoogleAnalyticsEnabled() ? 'Yes' : 'No'));
        $this->line('   Google Analytics ID: ' . ($analyticsService->getGoogleAnalyticsId() ?: 'Not set'));
        $this->line('   Facebook Pixel Enabled: ' . ($analyticsService->isFacebookPixelEnabled() ? 'Yes' : 'No'));
        $this->line('   Facebook Pixel ID: ' . ($analyticsService->getFacebookPixelId() ?: 'Not set'));
        $this->line('   Donation Tracking: ' . ($analyticsService->isDonationTrackingEnabled() ? 'Enabled' : 'Disabled'));
        $this->line('   Campaign View Tracking: ' . ($analyticsService->isCampaignViewTrackingEnabled() ? 'Enabled' : 'Disabled'));
        $this->newLine();

        // Test donation tracking
        $donationId = $this->option('donation-id');
        if ($donationId) {
            $donation = Donation::with('campaign')->find($donationId);
            if ($donation) {
                $this->info('2. Testing Donation Tracking:');
                $this->line('   Donation ID: ' . $donation->id);
                $this->line('   Amount: Rp ' . number_format($donation->amount, 0, ',', '.'));
                $this->line('   Campaign: ' . $donation->campaign->title);
                
                $analyticsService->trackDonation($donation);
                $this->line('   ✅ Donation event tracked successfully');
            } else {
                $this->error('   ❌ Donation not found with ID: ' . $donationId);
            }
            $this->newLine();
        }

        // Test campaign tracking
        $campaignId = $this->option('campaign-id');
        if ($campaignId) {
            $campaign = Campaign::with('category')->find($campaignId);
            if ($campaign) {
                $this->info('3. Testing Campaign View Tracking:');
                $this->line('   Campaign ID: ' . $campaign->id);
                $this->line('   Title: ' . $campaign->title);
                $this->line('   Category: ' . ($campaign->category->name ?? 'N/A'));
                
                $analyticsService->trackCampaignView($campaign);
                $this->line('   ✅ Campaign view event tracked successfully');
            } else {
                $this->error('   ❌ Campaign not found with ID: ' . $campaignId);
            }
            $this->newLine();
        }

        // Test script generation
        $this->info('4. Testing Script Generation:');
        $script = $analyticsService->generateAnalyticsScript();
        if ($script) {
            $this->line('   ✅ Analytics script generated successfully');
            $this->line('   Script length: ' . strlen($script) . ' characters');
        } else {
            $this->line('   ⚠️  No analytics script generated (analytics disabled)');
        }
        $this->newLine();

        // Show sample events
        $events = $analyticsService->getPendingEvents();
        if (!empty($events)) {
            $this->info('5. Pending Events:');
            foreach ($events as $event) {
                $this->line('   - ' . $event['event'] . ': ' . json_encode($event));
            }
        } else {
            $this->info('5. No pending events');
        }

        $this->newLine();
        $this->info('=== Test Completed ===');
    }
}
