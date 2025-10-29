<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Campaign;
use App\Models\Donation;
use Illuminate\Http\Request;

class TestDonationForm extends Command
{
    protected $signature = 'test:donation-form {campaign_slug}';
    protected $description = 'Test donation form submission dengan data minimal';

    public function handle()
    {
        $campaignSlug = $this->argument('campaign_slug');
        
        $this->info('🧪 Testing Donation Form Submission...');
        $this->info("📋 Campaign Slug: {$campaignSlug}");
        
        try {
            // Cari campaign
            $campaign = Campaign::where('slug', $campaignSlug)->first();
            
            if (!$campaign) {
                $this->error("❌ Campaign dengan slug '{$campaignSlug}' tidak ditemukan");
                return;
            }
            
            $this->info("✅ Campaign ditemukan: {$campaign->title}");
            
            // Simulasi request data minimal
            $requestData = [
                'amount' => 50000,
                'payment_method' => 'manual_bri',
                'is_anonymous' => true,
                // donor_name, donor_email, donor_whatsapp tidak diisi (opsional)
            ];
            
            $this->info('📝 Test data (minimal):');
            $this->line(json_encode($requestData, JSON_PRETTY_PRINT));
            
            // Test dengan data minimal
            $this->testDonationCreation($requestData, $campaign, 'Minimal Data');
            
            // Test dengan data lengkap
            $requestDataFull = [
                'amount' => 100000,
                'payment_method' => 'manual_bri',
                'donor_name' => 'John Doe',
                'donor_email' => 'john@example.com',
                'donor_whatsapp' => '08123456789',
                'message' => 'Semoga berkah',
                'is_anonymous' => false,
            ];
            
            $this->info('📝 Test data (full):');
            $this->line(json_encode($requestDataFull, JSON_PRETTY_PRINT));
            
            // Test dengan data lengkap
            $this->testDonationCreation($requestDataFull, $campaign, 'Full Data');
            
        } catch (\Exception $e) {
            $this->error('❌ Exception: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
        }
        
        $this->info('🏁 Test completed!');
    }
    
    private function testDonationCreation($requestData, $campaign, $testName)
    {
        $this->info("🔍 Testing {$testName}...");
        
        // Get available payment methods
        $bankAccounts = \App\Models\BankAccount::active()->get();
        $availablePaymentMethods = ['bank_transfer', 'e_wallet', 'qris'];
        foreach ($bankAccounts as $bank) {
            $availablePaymentMethods[] = 'manual_' . strtolower($bank->bank_name);
        }
        
        // Tambahkan VA Tripay yang diaktifkan
        $tripayService = new \App\Services\TripayService();
        $tripayChannels = $tripayService->getEnabledPaymentMethods();
        foreach ($tripayChannels as $channel) {
            $availablePaymentMethods[] = 'tripay_' . strtolower($channel->code);
        }
        
        $validator = \Validator::make($requestData, [
            'donor_name' => 'nullable|string|max:255',
            'donor_email' => 'nullable|email|max:255',
            'donor_whatsapp' => 'nullable|string|max:20',
            'amount' => 'required|integer|min:10000',
            'message' => 'nullable|string|max:1000',
            'payment_method' => 'required|in:' . implode(',', $availablePaymentMethods),
            'is_anonymous' => 'boolean',
            'salutation' => 'nullable|string|max:50',
        ]);
        
        if ($validator->fails()) {
            $this->error("❌ Validation failed for {$testName}:");
            foreach ($validator->errors()->all() as $error) {
                $this->line("  - {$error}");
            }
            return;
        }
        
        $this->info("✅ Validation passed for {$testName}!");
        
        // Test donation creation
        $this->info("💾 Testing donation creation for {$testName}...");
        
        $donorName = $requestData['donor_name'] ?? ($requestData['is_anonymous'] ? 'Hamba Allah' : 'Anonim');
        $donorWhatsapp = $requestData['donor_whatsapp'] ?? null;
        
        $donation = \App\Models\Donation::create([
            'campaign_id' => $campaign->id,
            'user_id' => null, // Guest donation
            'donor_name' => $donorName,
            'donor_email' => $requestData['donor_email'] ?? null,
            'donor_whatsapp' => $donorWhatsapp,
            'amount' => $requestData['amount'],
            'message' => $requestData['message'] ?? null,
            'payment_method' => $requestData['payment_method'],
            'payment_status' => 'pending',
            'expired_at' => now()->addHours(24),
            'is_anonymous' => $requestData['is_anonymous'] ?? false,
        ]);
        
        $this->info("✅ Donation created successfully for {$testName}!");
        $this->info("📊 Donation ID: {$donation->id}");
        $this->info("💰 Amount: Rp " . number_format($donation->amount));
        $this->info("👤 Donor Name: {$donation->donor_name}");
        $this->info("📧 Email: " . ($donation->donor_email ?: 'Not provided'));
        $this->info("📱 WhatsApp: " . ($donation->donor_whatsapp ?: 'Not provided'));
        $this->info("🔒 Anonymous: " . ($donation->is_anonymous ? 'Yes' : 'No'));
        
        // Cleanup - delete test donation
        $donation->delete();
        $this->info("🧹 Test donation cleaned up for {$testName}");
    }
} 