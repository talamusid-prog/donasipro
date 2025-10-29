<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $nodeUrl;
    protected $enabled;
    protected $method;

    public function __construct()
    {
        $this->method = config('whatsapp.method', 'web_api');
        $this->enabled = config('whatsapp.enabled', false);
        
        // Set URL berdasarkan method
        switch ($this->method) {
            case 'web_api':
                $this->nodeUrl = config('whatsapp.web_api.node_url', 'http://localhost:3001');
                $this->enabled = config('whatsapp.web_api.enabled', true);
                break;
            case 'cloud_api':
                $this->nodeUrl = config('whatsapp.cloud_api.url', 'https://graph.facebook.com/v18.0');
                $this->enabled = !!(config('whatsapp.cloud_api.token') && config('whatsapp.cloud_api.phone_id'));
                break;
            case 'gateway_api':
                $this->nodeUrl = config('whatsapp.gateway_api.url');
                $this->enabled = !!(config('whatsapp.gateway_api.token') && config('whatsapp.gateway_api.instance'));
                break;
            case 'wa_blast_api':
                $this->nodeUrl = config('whatsapp.wa_blast_api.base_url');
                $this->enabled = !!(config('whatsapp.wa_blast_api.api_key') && config('whatsapp.wa_blast_api.enabled'));
                break;
            default:
                $this->nodeUrl = config('whatsapp.node_url', 'http://localhost:3001');
                break;
        }
    }

    /**
     * Send notification message
     */
    public function sendNotification($phone, $message, $type = null, $variables = [])
    {
        if (!$this->enabled) {
            Log::info('WhatsApp service tidak diaktifkan');
            return [
                'success' => false,
                'message' => 'WhatsApp service tidak diaktifkan'
            ];
        }

        // Ambil template dari database jika type diberikan
        if ($type) {
            $template = \App\Models\WhatsAppTemplate::getByType($type);
            if ($template) {
                // Replace variabel dari $variables
                $message = $template->replaceVariables($variables);
            }
        }

        switch ($this->method) {
            case 'cloud_api':
                return $this->sendViaCloudAPI($phone, $message, $type);
            case 'web_api':
                return $this->sendViaWebAPI($phone, $message, $type);
            case 'gateway_api':
                return $this->sendViaGatewayAPI($phone, $message, $type);
            case 'wa_blast_api':
                return $this->sendViaWABlastAPI($phone, $message, $type);
            default:
                return $this->sendViaWebAPI($phone, $message, $type);
        }
    }

    /**
     * Send via WhatsApp Web API
     */
    protected function sendViaWebAPI($phone, $message, $type)
    {
        try {
            // Cek apakah server NodeJS tersedia
            if (!$this->isNodeServerAvailable()) {
                Log::info('NodeJS server tidak tersedia, menggunakan mode demo');
                return $this->sendDemoMessage($phone, $message, $type);
            }

            $response = Http::timeout(config('whatsapp.timeout', 30))
                           ->post($this->nodeUrl . '/whatsapp/send', [
                'phone' => $phone,
                'message' => $message
            ]);

            $data = $response->json();
            
            if (!is_array($data)) {
                return [
                    'success' => false,
                    'message' => 'Response tidak valid dari server WhatsApp'
                ];
            }
            
            return $data;

        } catch (\Exception $e) {
            Log::error('WhatsApp Web API error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Gagal kirim pesan: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send via WhatsApp Cloud API
     */
    protected function sendViaCloudAPI($phone, $message, $type)
    {
        try {
            $token = config('whatsapp.cloud_api.token');
            $phoneId = config('whatsapp.cloud_api.phone_id');

            if (!$token || !$phoneId) {
                return [
                    'success' => false,
                    'message' => 'WhatsApp Cloud API belum dikonfigurasi'
                ];
            }

            // Format phone number
            $formattedPhone = $this->formatPhoneNumber($phone);

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'Content-Type' => 'application/json'
            ])->post("https://graph.facebook.com/v18.0/{$phoneId}/messages", [
                'messaging_product' => 'whatsapp',
                'to' => $formattedPhone,
                'type' => 'text',
                'text' => [
                    'body' => $message
                ]
            ]);

            if ($response->successful()) {
                Log::info("WhatsApp Cloud API: Message sent to {$formattedPhone}");
                return [
                    'success' => true,
                    'message' => 'Pesan berhasil dikirim',
                    'method' => 'cloud_api'
                ];
            } else {
                Log::error('WhatsApp Cloud API error:', $response->json());
                return [
                    'success' => false,
                    'message' => 'Gagal kirim pesan: ' . ($response->json()['error']['message'] ?? 'Unknown error')
                ];
            }

        } catch (\Exception $e) {
            Log::error('WhatsApp Cloud API exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send via WhatsApp Gateway API
     */
    protected function sendViaGatewayAPI($phone, $message, $type)
    {
        try {
            $gatewayUrl = config('whatsapp.gateway_api.url');
            $gatewayToken = config('whatsapp.gateway_api.token');
            $gatewayInstance = config('whatsapp.gateway_api.instance');

            if (!$gatewayToken || !$gatewayInstance) {
                return [
                    'success' => false,
                    'message' => 'WhatsApp Gateway API belum dikonfigurasi'
                ];
            }

            $formattedPhone = $this->formatPhoneNumber($phone);

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$gatewayToken}",
                'Content-Type' => 'application/json'
            ])->post("{$gatewayUrl}/message/sendText/{$gatewayInstance}", [
                'number' => $formattedPhone,
                'text' => $message
            ]);

            if ($response->successful()) {
                Log::info("WhatsApp Gateway API: Message sent to {$formattedPhone}");
                return [
                    'success' => true,
                    'message' => 'Pesan berhasil dikirim',
                    'method' => 'gateway_api'
                ];
            } else {
                Log::error('WhatsApp Gateway API error:', $response->json());
                return [
                    'success' => false,
                    'message' => 'Gagal kirim pesan: ' . ($response->json()['message'] ?? 'Unknown error')
                ];
            }

        } catch (\Exception $e) {
            Log::error('WhatsApp Gateway API exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send via WA Blast API
     */
    protected function sendViaWABlastAPI($phone, $message, $type)
    {
        try {
            $baseUrl = \App\Models\AppSetting::where('key', 'wa_blast_base_url')->value('value') 
                      ?? config('whatsapp.wa_blast_api.base_url');
            $apiKey = \App\Models\AppSetting::where('key', 'wa_blast_api_key')->value('value') 
                     ?? config('whatsapp.wa_blast_api.api_key');

            if (!$apiKey || !$baseUrl) {
                return [
                    'success' => false,
                    'message' => 'WA Blast API belum dikonfigurasi'
                ];
            }

            $formattedPhone = $this->formatPhoneNumber($phone);

            // Gunakan session UUID dari database settings atau config
            $sessionUuid = \App\Models\AppSetting::where('key', 'wa_blast_session_uuid')->value('value') 
                          ?? config('whatsapp.wa_blast_api.session_uuid', '7d549d3d-a951-478e-b4d7-c90e465bd706');
            
            // Format body sebagai form data sesuai contoh PowerShell
            $formData = [
                'to_number' => $formattedPhone,
                'message_type' => 'text',
                'message' => $message
            ];
            
            // Endpoint kirim pesan tanpa /api/v1
            $url = rtrim($baseUrl, '/') . "/api/sessions/{$sessionUuid}/test-send";
            Log::info('WA Blast API Request URL: ' . $url);
            Log::info('WA Blast API Request Data: ' . json_encode($formData));

            // Gunakan asForm() untuk mengirim sebagai form data
            $response = Http::withHeaders([
                'X-API-Key' => $apiKey,
                'Accept' => 'application/json'
            ])->withoutVerifying() // Disable SSL verification for development
              ->asForm() // Kirim sebagai form data
              ->timeout(30) // Timeout 30 detik
              ->post($url, $formData);

            // Log response untuk debugging
            Log::info('WA Blast API Response Status: ' . $response->status());
            Log::info('WA Blast API Response Body: ' . $response->body());

            if ($response->successful()) {
                $data = $response->json();
                Log::info("WA Blast API: Message sent to {$formattedPhone}");
                return [
                    'success' => true,
                    'message' => 'Pesan berhasil dikirim',
                    'method' => 'wa_blast_api',
                    'data' => $data
                ];
            } else {
                $errorData = $response->json() ?: [];
                Log::error('WA Blast API error: ' . json_encode($errorData));
                return [
                    'success' => false,
                    'message' => 'Gagal kirim pesan: ' . ($errorData['message'] ?? 'Unknown error'),
                    'error_code' => $errorData['error_code'] ?? null
                ];
            }

        } catch (\Exception $e) {
            Log::error('WA Blast API exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send template message via WA Blast API
     */
    public function sendTemplateMessage($phone, $template, $variables = [])
    {
        if ($this->method !== 'wa_blast_api') {
            return [
                'success' => false,
                'message' => 'Template message hanya tersedia untuk WA Blast API'
            ];
        }

        try {
            $baseUrl = \App\Models\AppSetting::where('key', 'wa_blast_base_url')->value('value') 
                      ?? config('whatsapp.wa_blast_api.base_url');
            $apiKey = \App\Models\AppSetting::where('key', 'wa_blast_api_key')->value('value') 
                     ?? config('whatsapp.wa_blast_api.api_key');
            $sessionId = config('whatsapp.wa_blast_api.session_id', 1);

            if (!$apiKey || !$baseUrl) {
                return [
                    'success' => false,
                    'message' => 'WA Blast API belum dikonfigurasi'
                ];
            }

            $formattedPhone = $this->formatPhoneNumber($phone);

            $response = Http::withHeaders([
                'X-API-Key' => $apiKey,
                'Content-Type' => 'application/json'
            ])->withoutVerifying() // Disable SSL verification for development
              ->post("{$baseUrl}/integration/send-template", [
                'session_id' => $sessionId,
                'to_number' => $formattedPhone,
                'template' => $template,
                'variables' => $variables
            ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info("WA Blast API: Template message sent to {$formattedPhone}");
                return [
                    'success' => true,
                    'message' => 'Template pesan berhasil dikirim',
                    'method' => 'wa_blast_api',
                    'data' => $data
                ];
            } else {
                $errorData = $response->json() ?: [];
                Log::error('WA Blast API template error:', $errorData);
                return [
                    'success' => false,
                    'message' => 'Gagal kirim template pesan: ' . ($errorData['message'] ?? 'Unknown error'),
                    'error_code' => $errorData['error_code'] ?? null
                ];
            }

        } catch (\Exception $e) {
            Log::error('WA Blast API template exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }



    /**
     * Get WA Blast API system status
     */
    public function getWABlastStatus()
    {
        if ($this->method !== 'wa_blast_api') {
            return [
                'success' => false,
                'message' => 'Status hanya tersedia untuk WA Blast API'
            ];
        }

        try {
            $baseUrl = \App\Models\AppSetting::where('key', 'wa_blast_base_url')->value('value') 
                      ?? config('whatsapp.wa_blast_api.base_url');
            $apiKey = \App\Models\AppSetting::where('key', 'wa_blast_api_key')->value('value') 
                     ?? config('whatsapp.wa_blast_api.api_key');

            if (!$apiKey || !$baseUrl) {
                return [
                    'success' => false,
                    'message' => 'WA Blast API belum dikonfigurasi'
                ];
            }

            // Gunakan endpoint status dengan /api/v1
            $url = rtrim($baseUrl, '/') . '/api/v1/integration/system-status';

            $response = Http::withHeaders([
                'X-API-Key' => $apiKey
            ])->withoutVerifying() // Disable SSL verification for development
              ->get($url);

            // Log response untuk debugging
            Log::info('WA Blast API Response Status: ' . $response->status());
            Log::info('WA Blast API Response Body: ' . $response->body());

            if ($response->successful()) {
                $data = $response->json();
                Log::info('WA Blast API Success Data: ' . json_encode($data));
                return [
                    'success' => true,
                    'data' => $data
                ];
            } else {
                $errorData = $response->json() ?: [];
                Log::error('WA Blast API Error Response: ' . json_encode($errorData));
                Log::error('WA Blast API Error Status: ' . $response->status());
                return [
                    'success' => false,
                    'message' => 'Gagal cek status: ' . ($errorData['message'] ?? 'Unknown error'),
                    'status_code' => $response->status(),
                    'response_body' => $response->body()
                ];
            }

        } catch (\Exception $e) {
            Log::error('WA Blast API status exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Test WA Blast API connection (alias for getWABlastStatus)
     */
    public function testWABlastConnection()
    {
        return $this->getWABlastStatus();
    }

    /**
     * Send demo message (fallback)
     */
    protected function sendDemoMessage($phone, $message, $type)
    {
        Log::info("Demo WhatsApp: Message sent to {$phone}: {$message}");
        return [
            'success' => true,
            'message' => 'Pesan demo berhasil dikirim (server tidak tersedia)',
            'method' => 'demo',
            'demo' => true
        ];
    }

    /**
     * Format phone number
     */
    protected function formatPhoneNumber($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone); // Hanya angka
        if (str_starts_with($phone, '0')) {
            // Ganti 0 di depan dengan 62
            $phone = '62' . substr($phone, 1);
        }
        // Jika sudah 62 di depan, biarkan
        return $phone;
    }

    /**
     * Send donation notification
     */
    public function sendDonationNotification($donation)
    {
        $phone = $donation->donor_whatsapp ?? $donation->donor_phone ?? ($donation->user ? $donation->user->phone : null) ?? null;
        if (!$phone) {
            return [
                'success' => false,
                'message' => 'Nomor WhatsApp tidak tersedia'
            ];
        }
        $phone = $this->formatPhoneNumber($phone);
        $type = 'donation_confirmation';
        
        // Siapkan variabel dasar
        $variables = [
            'donor_name' => $donation->donor_name ?? ($donation->user->name ?? ''),
            'donation_id' => $donation->id,
            'campaign_title' => $donation->campaign->title ?? '',
            'amount' => number_format($donation->amount, 0, ',', '.'),
            'payment_method' => $donation->payment_method ?? '',
            'payment_status' => $this->getStatusText($donation->payment_status),
            'expired_at' => $donation->expired_at ? $donation->expired_at->format('d/m/Y H:i') : '',
            'payment_url' => $donation->payment_url ?? '',
            'hours_left' => $donation->expired_at ? $donation->expired_at->diffInHours(now()) : '',
        ];
        
        // Tambahkan informasi bank jika metode pembayaran manual
        if (str_starts_with($donation->payment_method, 'manual_')) {
            $bankName = strtoupper(str_replace('manual_', '', $donation->payment_method));
            $bankAccount = \App\Models\BankAccount::whereRaw('LOWER(bank_name) = ?', [strtolower($bankName)])->first();
            if ($bankAccount) {
                $variables['bank_name'] = $bankAccount->bank_name;
                $variables['account_number'] = $bankAccount->account_number;
                $variables['account_holder'] = $bankAccount->account_holder;
                $variables['bank_info'] = "🏦 *Transfer ke Rekening*:\n• Bank: {$bankAccount->bank_name}\n• No. Rekening: {$bankAccount->account_number}\n• Atas Nama: {$bankAccount->account_holder}";
            } else {
                $variables['bank_info'] = "🏦 *Transfer ke bank {$bankName}* (cek detail di halaman pembayaran)";
            }
        }
        // Tambahkan informasi Tripay jika metode pembayaran Tripay
        else if (str_starts_with($donation->payment_method, 'tripay_')) {
            $tripayData = null;
            if ($donation->tripay_reference) {
                $tripayService = new \App\Services\TripayService();
                $tripayData = $tripayService->getTransactionDetail($donation->tripay_reference);
            }
            if ($tripayData && isset($tripayData['success']) && $tripayData['success']) {
                $payCode = $tripayData['data']['pay_code'] ?? null;
                $instructions = $tripayData['data']['instructions'][0]['title'] ?? null;
                $variables['tripay_method'] = $instructions;
                $variables['pay_code'] = $payCode;
                $variables['tripay_info'] = "🏦 *Pembayaran via Tripay*:\n" . ($instructions ? "• Metode: {$instructions}\n" : '') . ($payCode ? "• No. VA/Pay Code: {$payCode}" : '');
            } else {
                $variables['tripay_info'] = "🏦 *Pembayaran via Tripay* (cek detail di halaman pembayaran)";
            }
        }
        // Metode pembayaran lain
        else {
            $variables['payment_info'] = "💳 *Metode Pembayaran:* " . strtoupper($donation->payment_method);
        }
        
        $message = $this->generateDonationMessage($donation); // fallback
        return $this->sendNotification($phone, $message, $type, $variables);
    }

    /**
     * Send donation notification using template message (WA Blast API)
     */
    protected function sendDonationTemplateMessage($donation, $phone)
    {
        $campaign = $donation->campaign;
        $amount = number_format($donation->amount, 0, ',', '.');
        
        $template = config('whatsapp.templates.donation_success.template');
        $variables = [
            'donation_id' => $donation->id,
            'campaign_title' => $campaign->title,
            'amount' => $amount,
            'status' => $this->getStatusText($donation->payment_status)
        ];

        return $this->sendTemplateMessage($phone, $template, $variables);
    }

    /**
     * Generate donation message
     */
    protected function generateDonationMessage($donation)
    {
        $campaign = $donation->campaign;
        $amount = number_format($donation->amount, 0, ',', '.');
        $paymentMethod = strtoupper($donation->payment_method);
        $paymentInfo = '';

        // Donasi manual bank transfer
        if (str_starts_with($donation->payment_method, 'manual_')) {
            $bankName = strtoupper(str_replace('manual_', '', $donation->payment_method));
            $bankAccount = \App\Models\BankAccount::whereRaw('LOWER(bank_name) = ?', [strtolower($bankName)])->first();
            if ($bankAccount) {
                $paymentInfo =
                    "\n🏦 *Transfer ke Rekening*:\n" .
                    "• Bank: {$bankAccount->bank_name}\n" .
                    "• No. Rekening: {$bankAccount->account_number}\n" .
                    "• Atas Nama: {$bankAccount->account_holder}";
            } else {
                $paymentInfo = "\n🏦 *Transfer ke bank {$bankName}* (cek detail di halaman pembayaran)";
            }
        }
        // Donasi Tripay (VA, e-wallet, dsb)
        else if (str_starts_with($donation->payment_method, 'tripay_')) {
            $tripayData = null;
            if ($donation->tripay_reference) {
                $tripayService = new \App\Services\TripayService();
                $tripayData = $tripayService->getTransactionDetail($donation->tripay_reference);
            }
            if ($tripayData && isset($tripayData['success']) && $tripayData['success']) {
                $payCode = $tripayData['data']['pay_code'] ?? null;
                $instructions = $tripayData['data']['instructions'][0]['title'] ?? null;
                $paymentInfo =
                    "\n🏦 *Pembayaran via Tripay*:\n" .
                    ($instructions ? "• Metode: {$instructions}\n" : '') .
                    ($payCode ? "• No. VA/Pay Code: {$payCode}" : '');
            } else {
                $paymentInfo = "\n🏦 *Pembayaran via Tripay* (cek detail di halaman pembayaran)";
            }
        }
        // Donasi lain (qris, e-wallet, dsb)
        else {
            $paymentInfo = "\n💳 *Metode Pembayaran:* {$paymentMethod}";
        }

        return "🎉 *Terima Kasih atas Donasi Anda!*\n\n" .
               "📋 *Detail Donasi:*\n" .
               "• ID: #{$donation->id}\n" .
               "• Program: {$campaign->title}\n" .
               "• Jumlah: Rp {$amount}\n" .
               "• Status: " . $this->getStatusText($donation->payment_status) .
               $paymentInfo .
               "\n\n🙏 Semoga amal Anda diterima Allah SWT.\n💝 Jazakumullahu khairan.";
    }

    /**
     * Get status text
     */
    protected function getStatusText($status)
    {
        return match($status) {
            'pending' => '⏳ Menunggu Pembayaran',
            'success' => '✅ Berhasil',
            'failed' => '❌ Gagal',
            default => '❓ Tidak Diketahui'
        };
    }

    /**
     * Generate QR Code untuk koneksi device (via NodeJS)
     */
    public function generateQRCode()
    {
        if (!$this->enabled) {
            return [
                'success' => false,
                'message' => 'WhatsApp service tidak diaktifkan'
            ];
        }

        // Mode demo untuk testing (jika server NodeJS tidak tersedia)
        if (!$this->isNodeServerAvailable()) {
            Log::info('NodeJS server tidak tersedia, menggunakan mode demo');
            return $this->generateDemoQR();
        }

        try {
            $response = Http::timeout(config('whatsapp.timeout', 30))
                           ->get($this->nodeUrl . '/whatsapp/qr');
            $data = $response->json();
            
            // Pastikan response selalu array yang valid
            if (!is_array($data)) {
                return [
                    'success' => false,
                    'message' => 'Response tidak valid dari server WhatsApp'
                ];
            }
            
            // Normalisasi response agar konsisten dengan view
            if (isset($data['success']) && $data['success']) {
                return [
                    'success' => true,
                    'qr_code' => $data['qr'] ?? '', // Gunakan qr dari server, return sebagai qr_code
                    'session_id' => $data['session_id'] ?? '',
                    'expires_at' => $data['expires_at'] ?? '',
                    'status' => $data['status'] ?? '',
                    'message' => $data['message'] ?? ''
                ];
            }
            
            return $data;
        } catch (\Exception $e) {
            Log::error('Error generate QR: ' . $e->getMessage());
            
            // Fallback ke mode demo jika server tidak tersedia
            Log::info('Falling back to demo mode');
            return $this->generateDemoQR();
        }
    }

    /**
     * Check QR status
     */
    public function checkQRStatus($sessionId)
    {
        if (!$this->enabled) {
            return [
                'success' => false,
                'message' => 'WhatsApp service tidak diaktifkan'
            ];
        }

        try {
            $response = Http::timeout(config('whatsapp.timeout', 30))
                           ->get($this->nodeUrl . '/whatsapp/qr-status', [
                'session_id' => $sessionId
            ]);
            $data = $response->json();
            
            // Pastikan response selalu array yang valid
            if (!is_array($data)) {
                return [
                    'success' => false,
                    'message' => 'Response tidak valid dari server WhatsApp'
                ];
            }
            
            return $data;
        } catch (\Exception $e) {
            Log::error('Error check QR status: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Gagal cek status QR: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Connect device (via NodeJS)
     */
    public function connectDevice($sessionId)
    {
        if (!$this->enabled) {
            return [
                'success' => false,
                'message' => 'WhatsApp service tidak diaktifkan'
            ];
        }

        try {
            $response = Http::timeout(config('whatsapp.timeout', 30))
                           ->post($this->nodeUrl . '/whatsapp/connect', [
                'session_id' => $sessionId
            ]);
            $data = $response->json();
            
            // Pastikan response selalu array yang valid
            if (!is_array($data)) {
                return [
                    'success' => false,
                    'message' => 'Response tidak valid dari server WhatsApp'
                ];
            }
            
            return $data;
        } catch (\Exception $e) {
            Log::error('Error connect device: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Gagal connect device: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Disconnect device (via NodeJS)
     */
    public function disconnectDevice()
    {
        if (!$this->enabled) {
            return [
                'success' => false,
                'message' => 'WhatsApp service tidak diaktifkan'
            ];
        }

        try {
            $response = Http::timeout(config('whatsapp.timeout', 30))
                           ->post($this->nodeUrl . '/whatsapp/disconnect');
            $data = $response->json();
            
            // Pastikan response selalu array yang valid
            if (!is_array($data)) {
                return [
                    'success' => false,
                    'message' => 'Response tidak valid dari server WhatsApp'
                ];
            }
            
            return $data;
        } catch (\Exception $e) {
            Log::error('Error disconnect device: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Gagal disconnect: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get WhatsApp status
     */
    public function getStatus()
    {
        if (!$this->enabled) {
            return [
                'success' => false,
                'message' => 'WhatsApp service tidak diaktifkan'
            ];
        }

        try {
            // Cek apakah server NodeJS tersedia
            if (!$this->isNodeServerAvailable()) {
                Log::info('NodeJS server tidak tersedia, menggunakan mode demo');
                return [
                    'success' => true,
                    'data' => [
                        'connected' => false,
                        'status' => 'demo',
                        'message' => 'Mode Demo - Server NodeJS tidak tersedia',
                        'method' => 'WhatsApp Web API'
                    ]
                ];
            }

            $response = Http::timeout(config('whatsapp.timeout', 30))
                           ->get($this->nodeUrl . '/whatsapp/status');
            $data = $response->json();
            
            // Pastikan response selalu array yang valid
            if (!is_array($data)) {
                return [
                    'success' => false,
                    'message' => 'Response tidak valid dari server WhatsApp'
                ];
            }
            
            return [
                'success' => true,
                'data' => $data
            ];
            
        } catch (\Exception $e) {
            Log::error('Error get status: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Gagal cek status: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get message logs
     */
    public function getMessageLog($limit = 10)
    {
        if (!$this->enabled) {
            return [];
        }

        try {
            // Cek apakah server NodeJS tersedia
        if (!$this->isNodeServerAvailable()) {
                Log::info('NodeJS server tidak tersedia, menggunakan mode demo');
            return $this->getDemoMessageLog($limit);
        }

            $response = Http::timeout(config('whatsapp.timeout', 30))
                           ->get($this->nodeUrl . '/whatsapp/logs');
            $data = $response->json();
            
            if (!is_array($data) || !isset($data['data'])) {
                return $this->getDemoMessageLog($limit);
            }
            
            $logs = is_array($data['data']) ? $data['data'] : [];
            
            // Bersihkan format nomor telepon
            foreach ($logs as &$log) {
                if (isset($log['to'])) {
                    $log['to'] = $this->cleanPhoneNumber($log['to']);
                }
            }
            
            return array_slice($logs, 0, $limit);
            
        } catch (\Exception $e) {
            Log::error('Error get message log: ' . $e->getMessage());
            return $this->getDemoMessageLog($limit);
        }
    }

    /**
     * Generate demo message log untuk testing
     */
    protected function getDemoMessageLog($limit = 10)
    {
        $demoLogs = [
            [
                'timestamp' => now()->subMinutes(5)->toISOString(),
                'to' => '628123456789',
                'message' => 'Test pesan demo dari sistem donasi',
                'status' => 'sent'
            ],
            [
                'timestamp' => now()->subMinutes(10)->toISOString(),
                'to' => '628987654321',
                'message' => 'Konfirmasi donasi berhasil',
                'status' => 'delivered'
            ],
            [
                'timestamp' => now()->subMinutes(15)->toISOString(),
                'to' => '628111222333',
                'message' => 'Reminder pembayaran donasi',
                'status' => 'read'
            ]
        ];

        return array_slice($demoLogs, 0, $limit);
    }

    /**
     * Clean phone number format
     */
    protected function cleanPhoneNumber($phone)
    {
        // Hapus @c.us dan karakter non-digit
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Pastikan format 62xxx
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }
        if (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }
        
        return $phone;
    }

    /**
     * Cek apakah server NodeJS tersedia
     */
    protected function isNodeServerAvailable()
    {
        try {
            // Coba akses endpoint health terlebih dahulu (lebih cepat)
            $response = Http::timeout(10)->get($this->nodeUrl . '/health');
            if ($response->successful()) {
                $data = $response->json();
                Log::info('NodeJS server tersedia: ' . $this->nodeUrl);
                return true;
            }
            
            // Jika health gagal, coba endpoint status
            $response = Http::timeout(10)->get($this->nodeUrl . '/whatsapp/status');
            if ($response->successful()) {
                $data = $response->json();
                Log::info('NodeJS server tersedia (via status): ' . $this->nodeUrl);
                return true;
            }
            
            Log::info('NodeJS server tidak merespon: ' . $this->nodeUrl);
            return false;
        } catch (\Exception $e) {
            Log::info('NodeJS server tidak tersedia: ' . $e->getMessage() . ' - URL: ' . $this->nodeUrl);
            return false;
        }
    }

    /**
     * Generate QR Code demo untuk testing
     */
    protected function generateDemoQR()
    {
        $sessionId = 'demo_' . uniqid();
        $qrData = json_encode([
            'session_id' => $sessionId,
            'timestamp' => time(),
            'demo' => true
        ]);
        
        // Generate QR code sederhana (base64 encoded)
        $qrCode = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';
        
        return [
            'success' => true,
            'qr_code' => $qrCode,
            'session_id' => $sessionId,
            'expires_at' => now()->addMinutes(2)->timestamp,
            'demo' => true,
            'message' => 'Demo QR Code - Server NodeJS tidak tersedia'
        ];
    }
} 