<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TripayService
{
    private $apiKey;
    private $privateKey;
    private $merchantCode;
    private $baseUrl;
    private $isProduction;

    public function __construct()
    {
        // Default values
        $this->apiKey = 'DEV-nytPwt4v7V2hoz4mmeHBy9ObqgNNGx7mH8xbVB6s';
        $this->privateKey = 'UTs7d-URVnW-7kQOf-A423U-Y5SwL';
        $this->merchantCode = 'T4777';
        $this->baseUrl = 'https://tripay.co.id/api-sandbox';
        $this->isProduction = false;

        // Try to load settings from database
        try {
            $settings = \App\Models\AppSetting::where('key', 'like', 'tripay_%')
                ->pluck('value', 'key')
                ->mapWithKeys(function ($value, $key) {
                    return [str_replace('tripay_', '', $key) => $value];
                });

            if ($settings->count() > 0) {
                $this->apiKey = $settings->get('api_key', $this->apiKey);
                $this->privateKey = $settings->get('private_key', $this->privateKey);
                $this->merchantCode = $settings->get('merchant_code', $this->merchantCode);
                $this->baseUrl = $settings->get('base_url', $this->baseUrl);
                $this->isProduction = filter_var($settings->get('is_production', '0'), FILTER_VALIDATE_BOOLEAN);
            }
        } catch (\Exception $e) {
            // If database is not available, use default values
            \Log::warning('Could not load Tripay settings from database: ' . $e->getMessage());
        }
    }

    /**
     * Set configuration dynamically
     */
    public function setConfig($config)
    {
        if (isset($config['api_key'])) {
            $this->apiKey = $config['api_key'];
        }
        if (isset($config['private_key'])) {
            $this->privateKey = $config['private_key'];
        }
        if (isset($config['merchant_code'])) {
            $this->merchantCode = $config['merchant_code'];
        }
        if (isset($config['base_url'])) {
            $this->baseUrl = $config['base_url'];
        }
        if (isset($config['is_production'])) {
            $this->isProduction = $config['is_production'];
        }
    }

    /**
     * Get payment channels
     */
    public function getPaymentChannels()
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get($this->baseUrl . '/merchant/payment-channel');

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Tripay API Error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('Tripay Service Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Test connection to Tripay API
     */
    public function testConnection()
    {
        try {
            $response = $this->getPaymentChannels();
            
            if ($response && isset($response['success']) && $response['success']) {
                return [
                    'success' => true,
                    'message' => 'Koneksi ke Tripay API berhasil'
                ];
            }
            
            return [
                'success' => false,
                'message' => 'Gagal terhubung ke Tripay API'
            ];
            
        } catch (\Exception $e) {
            Log::error('Tripay Connection Test Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Create transaction
     */
    public function createTransaction($data)
    {
        try {
            $payload = [
                'method' => $data['payment_method'],
                'merchant_ref' => $data['merchant_ref'],
                'amount' => $data['amount'],
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'],
                'order_items' => [
                    [
                        'name' => $data['item_name'],
                        'price' => $data['amount'],
                        'quantity' => 1,
                    ]
                ],
                'return_url' => route('donations.success', $data['donation_id']),
                'callback_url' => url('/api/tripay/callback'),
                'expired_time' => (time() + (24 * 60 * 60)), // 24 hours
                'signature' => $this->generateSignature($data['merchant_ref'], $data['amount'])
            ];

            Log::info('=== TRIPAY CREATE TRANSACTION REQUEST ===');
            Log::info('URL: ' . $this->baseUrl . '/transaction/create');
            Log::info('API Key: ' . substr($this->apiKey, 0, 10) . '...');
            Log::info('Merchant Code: ' . $this->merchantCode);
            Log::info('Payload: ' . json_encode($payload));
            Log::info('Generated Signature: ' . $payload['signature']);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->post($this->baseUrl . '/transaction/create', $payload);

            Log::info('=== TRIPAY CREATE TRANSACTION RESPONSE ===');
            Log::info('Status Code: ' . $response->status());
            Log::info('Response Body: ' . $response->body());

            if ($response->successful()) {
                $responseData = $response->json();
                Log::info('Response JSON: ' . json_encode($responseData));
                return $responseData;
            }

            Log::error('Tripay Create Transaction Error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('Tripay Service Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return null;
        }
    }

    /**
     * Get transaction detail
     */
    public function getTransactionDetail($reference)
    {
        try {
            Log::info('=== TRIPAY GET TRANSACTION DETAIL REQUEST ===');
            Log::info('URL: ' . $this->baseUrl . '/transaction/detail');
            Log::info('Reference: ' . $reference);
            Log::info('API Key: ' . substr($this->apiKey, 0, 10) . '...');

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get($this->baseUrl . '/transaction/detail', [
                'reference' => $reference
            ]);

            Log::info('=== TRIPAY GET TRANSACTION DETAIL RESPONSE ===');
            Log::info('Status Code: ' . $response->status());
            Log::info('Response Body: ' . $response->body());

            if ($response->successful()) {
                $responseData = $response->json();
                Log::info('Response JSON: ' . json_encode($responseData));
                return $responseData;
            }

            Log::error('Tripay Get Transaction Error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('Tripay Service Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return null;
        }
    }

    /**
     * Generate signature for transaction sesuai dokumentasi Tripay
     * $signature = hash_hmac('sha256', $merchantCode.$merchantRef.$amount, $privateKey);
     */
    private function generateSignature($merchantRef, $amount)
    {
        $data = $this->merchantCode . $merchantRef . $amount;
        return hash_hmac('sha256', $data, $this->privateKey);
    }

    /**
     * Verify callback signature
     */
    public function verifyCallback($data, $signature)
    {
        $expectedSignature = hash_hmac('sha256', 
            $data['merchant_ref'] . $data['amount'] . $data['status'], 
            $this->privateKey
        );

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Get VA payment methods
     */
    public function getVAPaymentMethods()
    {
        $channels = $this->getPaymentChannels();
        
        if (!$channels || !isset($channels['data'])) {
            return [];
        }

        $vaMethods = [];
        foreach ($channels['data'] as $channel) {
            if (in_array($channel['code'], ['BRIVA', 'MANDIRI', 'BNI', 'BCA'])) {
                $vaMethods[] = [
                    'code' => $channel['code'],
                    'name' => $channel['name'],
                    'logo' => $channel['icon_url'] ?? null,
                    'group' => $channel['group'] ?? 'Virtual Account'
                ];
            }
        }

        return $vaMethods;
    }

    /**
     * Sync payment channels from Tripay API to database
     */
    public function syncChannels()
    {
        try {
            $channels = $this->getPaymentChannels();
            
            if (!$channels || !isset($channels['data'])) {
                return [
                    'success' => false,
                    'message' => 'Gagal mengambil data channel dari Tripay API'
                ];
            }

            $syncedCount = 0;
            $updatedCount = 0;

            foreach ($channels['data'] as $channelData) {
                // Prepare data with safe defaults
                $channelData = array_merge([
                    'name' => '',
                    'group' => 'Unknown',
                    'type' => 'direct',
                    'icon_url' => null,
                    'active' => false,
                    'fee_merchant' => ['flat' => 0, 'percent' => 0],
                    'fee_customer' => ['flat' => 0, 'percent' => 0],
                    'total_fee' => ['flat' => 0, 'percent' => 0],
                    'minimum_fee' => 0,
                    'maximum_fee' => 0,
                    'minimum_amount' => 0,
                    'maximum_amount' => 0,
                ], $channelData);

                // Handle null values specifically
                $channelData['minimum_fee'] = $channelData['minimum_fee'] ?? 0;
                $channelData['maximum_fee'] = $channelData['maximum_fee'] ?? 0;
                $channelData['minimum_amount'] = $channelData['minimum_amount'] ?? 0;
                $channelData['maximum_amount'] = $channelData['maximum_amount'] ?? 0;

                $channel = \App\Models\TripayChannel::updateOrCreate(
                    ['code' => $channelData['code']],
                    [
                        'name' => $channelData['name'],
                        'group' => $channelData['group'],
                        'type' => $channelData['type'],
                        'icon_url' => $channelData['icon_url'],
                        'active' => $channelData['active'],
                        'fee_merchant_flat' => $channelData['fee_merchant']['flat'],
                        'fee_merchant_percent' => $channelData['fee_merchant']['percent'],
                        'fee_customer_flat' => $channelData['fee_customer']['flat'],
                        'fee_customer_percent' => $channelData['fee_customer']['percent'],
                        'total_fee_flat' => $channelData['total_fee']['flat'],
                        'total_fee_percent' => $channelData['total_fee']['percent'],
                        'minimum_fee' => $channelData['minimum_fee'],
                        'maximum_fee' => $channelData['maximum_fee'],
                        'minimum_amount' => $channelData['minimum_amount'],
                        'maximum_amount' => $channelData['maximum_amount'],
                    ]
                );

                if ($channel->wasRecentlyCreated) {
                    $syncedCount++;
                } else {
                    $updatedCount++;
                }
            }

            return [
                'success' => true,
                'message' => "Berhasil sync {$syncedCount} channel baru dan update {$updatedCount} channel",
                'data' => [
                    'synced' => $syncedCount,
                    'updated' => $updatedCount,
                    'total' => count($channels['data'])
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Tripay Sync Channels Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat sync channels: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get enabled payment methods for donation form
     */
    public function getEnabledPaymentMethods()
    {
        return \App\Models\TripayChannel::enabled()->active()->get();
    }

    /**
     * Get payment instructions
     */
    public function getPaymentInstructions($channelCode)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get($this->baseUrl . '/payment/instruction', [
                'code' => $channelCode
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Tripay Get Instructions Error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('Tripay Service Error: ' . $e->getMessage());
            return null;
        }
    }
} 