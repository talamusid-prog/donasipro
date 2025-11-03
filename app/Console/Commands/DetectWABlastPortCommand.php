<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DetectWABlastPortCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wa-blast:detect-port {--api-key= : API key untuk testing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deteksi port WA Blast API yang aktif';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Deteksi Port WA Blast API ===');
        
        // Daftar port yang umum digunakan
        $ports = [3000, 3001, 8080, 8000, 9000, 5000, 4000];
        
        // API key untuk testing
        $apiKey = $this->option('api-key') ?: 'your_api_key_here';
        
        $this->info('Mencari WA Blast server di port berikut:');
        foreach ($ports as $port) {
            $this->line("- Port {$port}");
        }
        $this->newLine();
        
        $foundPort = null;
        $foundResponse = null;
        
        $progressBar = $this->output->createProgressBar(count($ports));
        $progressBar->start();
        
        foreach ($ports as $port) {
            $url = "http://localhost:{$port}/api/v1/integration/system-status";
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'X-API-Key: ' . $apiKey,
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if (!$error && $httpCode === 200) {
                $foundPort = $port;
                $foundResponse = $response;
                break;
            }
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine(2);
        
        if ($foundPort) {
            $this->info("🎉 WA Blast server ditemukan di port {$foundPort}!");
            $this->newLine();
            
            $this->info('Update file .env Anda dengan:');
            $this->line("WA_BLAST_BASE_URL=http://localhost:{$foundPort}");
            $this->line("WA_BLAST_API_KEY=your_api_key_here");
            $this->line("WA_BLAST_SESSION_ID=1");
            $this->line("WA_BLAST_ENABLED=true");
            $this->line("WHATSAPP_METHOD=wa_blast_api");
            $this->newLine();
            
            if ($foundResponse) {
                $this->info('Response dari server:');
                $data = json_decode($foundResponse, true);
                if ($data) {
                    $this->line(json_encode($data, JSON_PRETTY_PRINT));
                } else {
                    $this->line($foundResponse);
                }
                $this->newLine();
            }
            
            $this->info('Langkah selanjutnya:');
            $this->line('1. Update file .env dengan konfigurasi di atas');
            $this->line('2. Clear cache: php artisan config:clear');
            $this->line('3. Test koneksi: php artisan wa-blast:test');
            
        } else {
            $this->error('❌ WA Blast server tidak ditemukan di port yang umum digunakan.');
            $this->newLine();
            
            $this->warn('Kemungkinan penyebab:');
            $this->line('1. WA Blast server belum dijalankan');
            $this->line('2. WA Blast berjalan di port yang tidak standar');
            $this->line('3. Firewall memblokir koneksi');
            $this->line('4. WA Blast menggunakan endpoint yang berbeda');
            $this->newLine();
            
            $this->info('Solusi:');
            $this->line('1. Jalankan WA Blast server terlebih dahulu');
            $this->line('2. Cek dokumentasi WA Blast untuk port default');
            $this->line('3. Cek file konfigurasi WA Blast');
            $this->line('4. Cek firewall settings');
            $this->line('5. Coba port lain secara manual');
        }
        
        $this->newLine();
        $this->info('=== Deteksi Selesai ===');
        
        return $foundPort ? 0 : 1;
    }
} 