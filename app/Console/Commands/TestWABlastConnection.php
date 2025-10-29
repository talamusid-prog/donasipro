<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;

class TestWABlastConnection extends Command
{
    protected $signature = 'wa-blast:test-connection';
    protected $description = 'Test koneksi WA Blast API';

    public function handle()
    {
        $this->info('🔍 Testing WA Blast API Connection...');
        
        try {
            $whatsappService = new WhatsAppService();
            
            // Test 1: Cek konfigurasi
            $this->info('📋 Checking configuration...');
            $config = [
                'base_url' => config('whatsapp.wa_blast_api.base_url'),
                'api_key' => config('whatsapp.wa_blast_api.api_key'),
                'session_uuid' => config('whatsapp.wa_blast_api.session_uuid'),
                'enabled' => config('whatsapp.wa_blast_api.enabled'),
            ];
            
            $this->table(['Setting', 'Value'], [
                ['Base URL', $config['base_url']],
                ['API Key', $config['api_key'] ? '✓ Set' : '✗ Not Set'],
                ['Session UUID', $config['session_uuid']],
                ['Enabled', $config['enabled'] ? '✓ Yes' : '✗ No'],
            ]);
            
            // Test 2: Cek status koneksi
            $this->info('🔌 Testing connection status...');
            $status = $whatsappService->getWABlastStatus();
            
            if ($status['success']) {
                $this->info('✅ Connection successful!');
                $this->info('📊 Status data:');
                $this->line(json_encode($status['data'], JSON_PRETTY_PRINT));
            } else {
                $this->error('❌ Connection failed: ' . $status['message']);
            }
            
            // Test 3: Cek endpoint status
            $this->info('🌐 Testing endpoint directly...');
            $baseUrl = config('whatsapp.wa_blast_api.base_url');
            $apiKey = config('whatsapp.wa_blast_api.api_key');
            $sessionUuid = config('whatsapp.wa_blast_api.session_uuid');
            
            $endpoint = $baseUrl . '/api/v1/integration/system-status';
            $this->line("Endpoint: {$endpoint}");
            
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $endpoint,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTPHEADER => [
                    'X-API-Key: ' . $apiKey,
                    'Content-Type: application/json',
                ],
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            $this->info("HTTP Code: {$httpCode}");
            
            if ($error) {
                $this->error("cURL Error: {$error}");
            } else {
                $this->info("Response: {$response}");
                
                $data = json_decode($response, true);
                if ($data) {
                    $this->info('✅ Valid JSON response received');
                } else {
                    $this->warn('⚠️ Response is not valid JSON');
                }
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Exception: ' . $e->getMessage());
            Log::error('WA Blast test connection error: ' . $e->getMessage());
        }
        
        $this->info('🏁 Test completed!');
    }
} 