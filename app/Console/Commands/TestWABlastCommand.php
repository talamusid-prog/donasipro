<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WhatsAppService;

class TestWABlastCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wa-blast:test 
                            {--phone= : Nomor telepon untuk test}
                            {--message= : Pesan untuk test}
                            {--template= : Template untuk test}
                            {--variables= : Variables dalam format JSON}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test koneksi dan kirim pesan via WA Blast API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Test WA Blast API ===');
        
        $whatsappService = new WhatsAppService();
        
        // Test 1: Cek Status Koneksi
        $this->info('1. Testing koneksi...');
        $status = $whatsappService->getWABlastStatus();
        
        if ($status['success']) {
            $this->info('✅ Koneksi berhasil!');
            $this->line("   Message: {$status['message']}");
        } else {
            $this->error('❌ Koneksi gagal!');
            $this->line("   Error: {$status['message']}");
            return 1;
        }
        
        $this->newLine();
        
        // Test 2: Kirim Pesan (jika ada parameter)
        $phone = $this->option('phone');
        $message = $this->option('message');
        
        if ($phone && $message) {
            $this->info('2. Testing kirim pesan...');
            $this->line("   To: {$phone}");
            $this->line("   Message: {$message}");
            
            try {
                $result = $whatsappService->sendMessage($phone, $message, 'wa_blast_api');
                
                if ($result['success']) {
                    $this->info('✅ Pesan berhasil dikirim!');
                    if (isset($result['data']['message_id'])) {
                        $this->line("   Message ID: {$result['data']['message_id']}");
                    }
                } else {
                    $this->error('❌ Gagal mengirim pesan!');
                    $this->line("   Error: {$result['message']}");
                }
            } catch (\Exception $e) {
                $this->error('❌ Exception: ' . $e->getMessage());
            }
            
            $this->newLine();
        }
        
        // Test 3: Kirim Template (jika ada parameter)
        $template = $this->option('template');
        $variables = $this->option('variables');
        
        if ($phone && $template) {
            $this->info('3. Testing template message...');
            $this->line("   To: {$phone}");
            $this->line("   Template: {$template}");
            
            $vars = [];
            if ($variables) {
                try {
                    $vars = json_decode($variables, true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new \Exception('Invalid JSON format');
                    }
                    $this->line("   Variables: {$variables}");
                } catch (\Exception $e) {
                    $this->error('❌ Invalid variables format: ' . $e->getMessage());
                    return 1;
                }
            }
            
            try {
                $result = $whatsappService->sendTemplateMessage($phone, $template, $vars, 'wa_blast_api');
                
                if ($result['success']) {
                    $this->info('✅ Template berhasil dikirim!');
                    if (isset($result['data']['message_id'])) {
                        $this->line("   Message ID: {$result['data']['message_id']}");
                    }
                } else {
                    $this->error('❌ Gagal mengirim template!');
                    $this->line("   Error: {$result['message']}");
                }
            } catch (\Exception $e) {
                $this->error('❌ Exception: ' . $e->getMessage());
            }
            
            $this->newLine();
        }
        
        // Test 4: Interactive test (jika tidak ada parameter)
        if (!$phone && !$message && !$template) {
            $this->info('4. Interactive test...');
            
            $testPhone = $this->ask('Masukkan nomor telepon untuk test (format: 6281234567890)');
            if (!$testPhone) {
                $this->warn('Nomor telepon tidak diisi, skip test kirim pesan');
                return 0;
            }
            
            $testMessage = $this->ask('Masukkan pesan untuk test', 'Test pesan dari aplikasi donasi - ' . date('Y-m-d H:i:s'));
            
            $this->info('Mengirim pesan test...');
            try {
                $result = $whatsappService->sendMessage($testPhone, $testMessage, 'wa_blast_api');
                
                if ($result['success']) {
                    $this->info('✅ Pesan test berhasil dikirim!');
                    $this->line("   Cek WhatsApp Anda untuk melihat pesan");
                } else {
                    $this->error('❌ Gagal mengirim pesan test!');
                    $this->line("   Error: {$result['message']}");
                }
            } catch (\Exception $e) {
                $this->error('❌ Exception: ' . $e->getMessage());
            }
        }
        
        $this->info('=== Test selesai ===');
        return 0;
    }
} 