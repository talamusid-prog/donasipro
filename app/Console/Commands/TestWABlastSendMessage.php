<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WhatsAppService;

class TestWABlastSendMessage extends Command
{
    protected $signature = 'wa-blast:test-send {phone} {message?}';
    protected $description = 'Test pengiriman pesan via WA Blast API';

    public function handle()
    {
        $phone = $this->argument('phone');
        $message = $this->argument('message') ?? 'Test pesan dari WA Blast API - ' . now();
        
        $this->info('📱 Testing WA Blast API Message Sending...');
        $this->info("📞 Phone: {$phone}");
        $this->info("💬 Message: {$message}");
        
        try {
            $whatsappService = new WhatsAppService();
            
            $result = $whatsappService->sendNotification($phone, $message, 'test');
            
            if ($result['success']) {
                $this->info('✅ Message sent successfully!');
                $this->info('📊 Response:');
                $this->line(json_encode($result, JSON_PRETTY_PRINT));
            } else {
                $this->error('❌ Failed to send message: ' . $result['message']);
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Exception: ' . $e->getMessage());
        }
        
        $this->info('🏁 Test completed!');
    }
} 