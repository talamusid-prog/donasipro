<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    /**
     * Handle WhatsApp webhook verification
     */
    public function verify(Request $request)
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        $verifyToken = config('services.whatsapp.verify_token');

        if ($mode === 'subscribe' && $token === $verifyToken) {
            Log::info('WhatsApp webhook verified successfully');
            return response($challenge, 200);
        }

        Log::error('WhatsApp webhook verification failed', [
            'mode' => $mode,
            'token' => $token,
            'expected_token' => $verifyToken
        ]);

        return response('Forbidden', 403);
    }

    /**
     * Handle WhatsApp webhook messages
     */
    public function handle(Request $request)
    {
        $payload = $request->all();
        
        Log::info('WhatsApp webhook received', $payload);

        // Handle different types of webhooks
        if (isset($payload['entry'][0]['changes'][0]['value']['messages'])) {
            $this->handleIncomingMessage($payload);
        } elseif (isset($payload['entry'][0]['changes'][0]['value']['statuses'])) {
            $this->handleMessageStatus($payload);
        }

        return response('OK', 200);
    }

    /**
     * Handle incoming messages (if needed)
     */
    protected function handleIncomingMessage($payload)
    {
        $messages = $payload['entry'][0]['changes'][0]['value']['messages'];
        
        foreach ($messages as $message) {
            Log::info('Incoming WhatsApp message', $message);
            
            // Handle different message types
            if (isset($message['text'])) {
                $this->handleTextMessage($message);
            }
        }
    }

    /**
     * Handle message status updates
     */
    protected function handleMessageStatus($payload)
    {
        $statuses = $payload['entry'][0]['changes'][0]['value']['statuses'];
        
        foreach ($statuses as $status) {
            Log::info('WhatsApp message status update', $status);
            
            // Update message status in database if needed
            $this->updateMessageStatus($status);
        }
    }

    /**
     * Handle text messages
     */
    protected function handleTextMessage($message)
    {
        $text = $message['text']['body'] ?? '';
        $from = $message['from'] ?? '';
        
        Log::info('Received text message', [
            'from' => $from,
            'text' => $text
        ]);

        // Handle specific commands if needed
        if (strtolower($text) === 'status') {
            // Send donation status
            $this->sendDonationStatus($from);
        }
    }

    /**
     * Update message status in database
     */
    protected function updateMessageStatus($status)
    {
        $messageId = $status['id'] ?? null;
        $statusType = $status['status'] ?? null;
        
        if ($messageId && $statusType) {
            Log::info('Message status updated', [
                'message_id' => $messageId,
                'status' => $statusType
            ]);
            
            // Update status in database if you're tracking message status
            // MessageStatus::updateOrCreate(['message_id' => $messageId], ['status' => $statusType]);
        }
    }

    /**
     * Send donation status to user
     */
    protected function sendDonationStatus($phoneNumber)
    {
        try {
            $whatsappService = new \App\Services\WhatsAppService();
            
            // Find user's recent donations
            $donations = \App\Models\Donation::where('donor_whatsapp', $phoneNumber)
                ->latest()
                ->take(5)
                ->get();

            if ($donations->count() > 0) {
                $message = "📋 *Status Donasi Terbaru Anda:*\n\n";
                
                foreach ($donations as $donation) {
                    $amount = number_format($donation->amount, 0, ',', '.');
                    $status = $this->getPaymentStatusName($donation->payment_status);
                    
                    $message .= "• {$donation->campaign->title}\n";
                    $message .= "  Rp {$amount} - {$status}\n";
                    $message .= "  ID: #{$donation->id}\n\n";
                }
                
                $whatsappService->sendMessage($phoneNumber, $message);
            } else {
                $message = "Maaf, kami tidak menemukan riwayat donasi untuk nomor ini.";
                $whatsappService->sendMessage($phoneNumber, $message);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send donation status', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get payment status name
     */
    protected function getPaymentStatusName($status)
    {
        $statuses = [
            'pending' => 'Menunggu Pembayaran',
            'paid' => 'Dibayar',
            'expired' => 'Kadaluarsa',
            'cancelled' => 'Dibatalkan',
        ];

        return $statuses[$status] ?? $status;
    }
} 