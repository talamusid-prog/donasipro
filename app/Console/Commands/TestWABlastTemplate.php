<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WhatsAppService;
use App\Models\WhatsAppTemplate;

class TestWABlastTemplate extends Command
{
    protected $signature = 'wa-blast:test-template {phone} {type}';
    protected $description = 'Test pengiriman template message via WA Blast API';

    public function handle()
    {
        $phone = $this->argument('phone');
        $type = $this->argument('type');
        
        $this->info('📱 Testing WA Blast API Template Message...');
        $this->info("📞 Phone: {$phone}");
        $this->info("📋 Template Type: {$type}");
        
        try {
            // Ambil template dari database
            $template = WhatsAppTemplate::getByType($type);
            
            if (!$template) {
                $this->error("❌ Template type '{$type}' tidak ditemukan");
                $this->info('📋 Available templates:');
                $templates = WhatsAppTemplate::all();
                foreach ($templates as $t) {
                    $this->line("- {$t->type}: {$t->title}");
                }
                return;
            }
            
            $this->info("📝 Template: {$template->title}");
            $this->info("📄 Content: {$template->content}");
            
            // Variables untuk testing
            $variables = [
                'donation_id' => 'TEST-' . rand(1000, 9999),
                'campaign_title' => 'Test Campaign',
                'amount' => '100,000',
                'status' => 'Pending',
                'expired_at' => now()->addDays(1)->format('d/m/Y H:i'),
                'payment_url' => 'https://example.com/payment',
                'payment_method' => 'Bank Transfer',
                'paid_at' => now()->format('d/m/Y H:i'),
                'bank_name' => 'Bank BCA',
                'bank_account' => '1234567890',
                'bank_holder' => 'Yayasan Test'
            ];
            
            $this->info('🔧 Variables:');
            $this->line(json_encode($variables, JSON_PRETTY_PRINT));
            
            // Replace variables
            $message = $template->replaceVariables($variables);
            $this->info("📤 Final Message: {$message}");
            
            $whatsappService = new WhatsAppService();
            $result = $whatsappService->sendNotification($phone, $message, $type, $variables);
            
            if ($result['success']) {
                $this->info('✅ Template message sent successfully!');
                $this->info('📊 Response:');
                $this->line(json_encode($result, JSON_PRETTY_PRINT));
            } else {
                $this->error('❌ Failed to send template message: ' . $result['message']);
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Exception: ' . $e->getMessage());
        }
        
        $this->info('🏁 Test completed!');
    }
} 