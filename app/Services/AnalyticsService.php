<?php

namespace App\Services;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Log;

class AnalyticsService
{
    protected $settings;

    public function __construct()
    {
        $this->settings = AppSetting::getAllAsArray();
    }

    /**
     * Check if Google Analytics is enabled
     */
    public function isGoogleAnalyticsEnabled()
    {
        return $this->settings['google_analytics_enabled'] ?? false;
    }

    /**
     * Get Google Analytics ID
     */
    public function getGoogleAnalyticsId()
    {
        return $this->settings['google_analytics_id'] ?? '';
    }

    /**
     * Check if Facebook Pixel is enabled
     */
    public function isFacebookPixelEnabled()
    {
        return $this->settings['facebook_pixel_enabled'] ?? false;
    }

    /**
     * Get Facebook Pixel ID
     */
    public function getFacebookPixelId()
    {
        return $this->settings['facebook_pixel_id'] ?? '';
    }

    /**
     * Check if donation tracking is enabled
     */
    public function isDonationTrackingEnabled()
    {
        return $this->settings['analytics_track_donations'] ?? true;
    }

    /**
     * Check if campaign view tracking is enabled
     */
    public function isCampaignViewTrackingEnabled()
    {
        return $this->settings['analytics_track_campaign_views'] ?? true;
    }

    /**
     * Track donation event
     */
    public function trackDonation($donation)
    {
        if (!$this->isDonationTrackingEnabled()) {
            return;
        }

        $eventData = [
            'event' => 'donation_completed',
            'donation_id' => $donation->id,
            'amount' => $donation->amount,
            'campaign_id' => $donation->campaign_id,
            'campaign_title' => $donation->campaign->title,
            'payment_method' => $donation->payment_method,
            'donor_type' => $donation->is_anonymous ? 'anonymous' : 'registered',
        ];

        $this->trackEvent($eventData);
    }

    /**
     * Track campaign view
     */
    public function trackCampaignView($campaign)
    {
        if (!$this->isCampaignViewTrackingEnabled()) {
            return;
        }

        $eventData = [
            'event' => 'campaign_view',
            'campaign_id' => $campaign->id,
            'campaign_title' => $campaign->title,
            'category' => $campaign->category->name ?? '',
            'target_amount' => $campaign->target_amount,
            'current_amount' => $campaign->current_amount,
        ];

        $this->trackEvent($eventData);
    }

    /**
     * Track custom event
     */
    public function trackEvent($eventData)
    {
        try {
            // Log event for debugging
            Log::info('Analytics Event: ' . json_encode($eventData));

            // Store event in session for JavaScript tracking
            session()->push('analytics_events', $eventData);
        } catch (\Exception $e) {
            Log::error('Failed to track analytics event: ' . $e->getMessage());
        }
    }

    /**
     * Get all pending events
     */
    public function getPendingEvents()
    {
        $events = session()->get('analytics_events', []);
        session()->forget('analytics_events');
        return $events;
    }

    /**
     * Generate Google Analytics script
     */
    public function generateGoogleAnalyticsScript()
    {
        if (!$this->isGoogleAnalyticsEnabled() || !$this->getGoogleAnalyticsId()) {
            return '';
        }

        $gaId = $this->getGoogleAnalyticsId();
        
        return "
        <!-- Google Analytics -->
        <script async src=\"https://www.googletagmanager.com/gtag/js?id={$gaId}\"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{$gaId}');
        </script>
        ";
    }

    /**
     * Generate Facebook Pixel script
     */
    public function generateFacebookPixelScript()
    {
        if (!$this->isFacebookPixelEnabled() || !$this->getFacebookPixelId()) {
            return '';
        }

        $pixelId = $this->getFacebookPixelId();
        
        return "
        <!-- Facebook Pixel -->
        <script>
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '{$pixelId}');
            fbq('track', 'PageView');
        </script>
        <noscript>
            <img height=\"1\" width=\"1\" style=\"display:none\"
                src=\"https://www.facebook.com/tr?id={$pixelId}&ev=PageView&noscript=1\"/>
        </noscript>
        ";
    }

    /**
     * Generate analytics events script
     */
    public function generateAnalyticsEventsScript()
    {
        $events = $this->getPendingEvents();
        if (empty($events)) {
            return '';
        }

        $script = "<script>\n";
        
        foreach ($events as $event) {
            $eventName = $event['event'];
            $eventParams = array_diff_key($event, ['event' => '']);
            
            // Google Analytics event
            if ($this->isGoogleAnalyticsEnabled()) {
                $params = json_encode($eventParams);
                $script .= "gtag('event', '{$eventName}', {$params});\n";
            }
            
            // Facebook Pixel event
            if ($this->isFacebookPixelEnabled()) {
                $value = $eventParams['amount'] ?? 0;
                $currency = 'IDR';
                $script .= "fbq('track', '{$eventName}', {value: {$value}, currency: '{$currency}'});\n";
            }
        }
        
        $script .= "</script>";
        return $script;
    }

    /**
     * Generate complete analytics script
     */
    public function generateAnalyticsScript()
    {
        $script = '';
        
        // Google Analytics
        $script .= $this->generateGoogleAnalyticsScript();
        
        // Facebook Pixel
        $script .= $this->generateFacebookPixelScript();
        
        // Analytics Events
        $script .= $this->generateAnalyticsEventsScript();
        
        return $script;
    }
} 