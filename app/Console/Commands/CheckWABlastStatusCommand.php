<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WhatsAppService;

class CheckWABlastStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wa-blast:check-status {--fix : Auto fix configuration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check WA Blast API status and configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== WA BLAST API STATUS CHECK ===');
        $this->newLine();

        // 1. Check active ports
        $this->info('1. Checking active ports...');
        $ports = [3000, 8080, 8000, 5000, 4000];
        $activePorts = [];

        foreach ($ports as $port) {
            $connection = @fsockopen('localhost', $port, $errno, $errstr, 2);
            if (is_resource($connection)) {
                $activePorts[] = $port;
                fclose($connection);
            }
        }

        if (empty($activePorts)) {
            $this->error('   ❌ No active ports found');
            $this->warn('   💡 Make sure WA Blast application is running');
            $this->newLine();
        } else {
            $this->info('   ✅ Active ports found: ' . implode(', ', $activePorts));
            $this->newLine();
        }

        // 2. Test WA Blast API connection
        $this->info('2. Testing WA Blast API connection...');
        $testPorts = array_merge($ports, $activePorts);
        $testPorts = array_unique($testPorts);
        $workingPort = null;

        foreach ($testPorts as $port) {
            $this->line("   Testing port $port... ");
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://localhost:$port/api/v1/integration/system-status");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'X-API-Key: wa-blast-lpFsrEK3wFuT0sUxzYmwafLvERwk2C8W'
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($error) {
                $this->error("❌ Error: $error");
            } elseif ($httpCode == 200) {
                $this->info("✅ Success! HTTP $httpCode");
                $this->line("   Response: " . substr($response, 0, 100) . "...");
                $workingPort = $port;
                break;
            } else {
                $this->error("❌ HTTP $httpCode");
            }
        }

        // 3. Check Laravel configuration
        $this->info('3. Checking Laravel configuration...');
        $baseUrl = config('whatsapp.wa_blast_api.base_url');
        
        if ($baseUrl) {
            $this->info("   ✅ Base URL configured: $baseUrl");
            
            if (strpos($baseUrl, '8080') !== false && $workingPort && $workingPort != 8080) {
                $this->warn("   ⚠️  Port 8080 not working, correct port: $workingPort");
                
                if ($this->option('fix')) {
                    $this->fixConfiguration($workingPort);
                } else {
                    $this->line("   💡 Update configuration to: http://localhost:$workingPort");
                }
            }
        } else {
            $this->error("   ❌ Base URL not configured");
        }

        // 4. Test WhatsApp service
        $this->info('4. Testing WhatsApp service...');
        try {
            $whatsappService = new WhatsAppService();
            $status = $whatsappService->testWABlastConnection();
            
            if ($status['success']) {
                $this->info("   ✅ WhatsApp service working");
                $this->line("   📋 Method: " . config('whatsapp.default_method'));
            } else {
                $this->error("   ❌ WhatsApp service error: " . $status['message']);
            }
        } catch (\Exception $e) {
            $this->error("   ❌ WhatsApp service exception: " . $e->getMessage());
        }

        $this->newLine();
        $this->info('=== SOLUTIONS ===');
        $this->line('a) Make sure WA Blast application is running');
        $this->line('b) Update .env configuration:');
        $this->line('   WA_BLAST_API_BASE_URL=http://localhost:3000');
        $this->line('c) Clear Laravel cache:');
        $this->line('   php artisan config:clear');
        $this->line('   php artisan cache:clear');
        $this->line('d) Test connection again');
        $this->newLine();

        if ($this->option('fix') && $workingPort) {
            $this->fixConfiguration($workingPort);
        }

        $this->info('=== COMPLETED ===');
    }

    private function fixConfiguration($port)
    {
        $this->info("🔧 Auto-fixing configuration to port $port...");
        
        // Update .env file
        $envFile = base_path('.env');
        if (file_exists($envFile)) {
            $envContent = file_get_contents($envFile);
            $newBaseUrl = "http://localhost:$port";
            
            if (strpos($envContent, 'WA_BLAST_API_BASE_URL=') !== false) {
                $envContent = preg_replace(
                    '/WA_BLAST_API_BASE_URL=.*/',
                    "WA_BLAST_API_BASE_URL=$newBaseUrl",
                    $envContent
                );
            } else {
                $envContent .= "\nWA_BLAST_API_BASE_URL=$newBaseUrl";
            }
            
            file_put_contents($envFile, $envContent);
            $this->info("   ✅ Updated .env file");
        }

        // Clear cache
        $this->call('config:clear');
        $this->call('cache:clear');
        $this->info("   ✅ Cleared cache");
        
        $this->info("   🎉 Configuration fixed! Test again with: php artisan wa-blast:check-status");
    }
} 