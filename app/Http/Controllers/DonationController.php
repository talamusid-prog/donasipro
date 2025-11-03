<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\TripayService;
use App\Services\WhatsAppService;
use App\Models\BankAccount;

class DonationController extends Controller
{
    public function store(Request $request, Campaign $campaign)
    {
        // Get available payment methods from bank accounts
        $bankAccounts = \App\Models\BankAccount::active()->get();
        $availablePaymentMethods = ['bank_transfer', 'e_wallet', 'qris'];
        foreach ($bankAccounts as $bank) {
            $availablePaymentMethods[] = 'manual_' . strtolower($bank->bank_name);
        }
        
        // Tambahkan VA Tripay yang diaktifkan
        $tripayService = new TripayService();
        $tripayChannels = $tripayService->getEnabledPaymentMethods();
        foreach ($tripayChannels as $channel) {
            $availablePaymentMethods[] = 'tripay_' . strtolower($channel->code);
        }

        $request->validate([
            'donor_name' => 'nullable|string|max:255',
            'donor_email' => 'nullable|email|max:255',
            'donor_whatsapp' => 'nullable|string|max:20',
            'amount' => 'required|integer|min:10000',
            'message' => 'nullable|string|max:1000',
            'payment_method' => 'required|in:' . implode(',', $availablePaymentMethods),
            'is_anonymous' => 'boolean',
            'salutation' => 'nullable|string|max:50',
        ]);

        try {
            DB::beginTransaction();

            // Handle nullable fields
            $donorName = $request->donor_name ?: ($request->is_anonymous ? 'Hamba Allah' : 'Anonim');
            $donorWhatsapp = $request->donor_whatsapp ?: null;
            
            // Create donation record
            $donation = Donation::create([
                'campaign_id' => $campaign->id,
                'user_id' => auth()->id(),
                'donor_name' => $donorName,
                'donor_email' => $request->donor_email,
                'donor_whatsapp' => $donorWhatsapp,
                'amount' => $request->amount,
                'message' => $request->message,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'expired_at' => now()->addHours(24),
                'is_anonymous' => $request->has('is_anonymous'),
            ]);

            // Jika Tripay VA
            if (str_starts_with($request->payment_method, 'tripay_')) {
                $vaCode = strtoupper(str_replace('tripay_', '', $request->payment_method));
                $merchantRef = 'DONASI-' . $donation->id . '-' . time();
                $tripayData = [
                    'payment_method' => $vaCode,
                    'merchant_ref' => $merchantRef,
                    'amount' => $donation->amount,
                    'customer_name' => $donation->donor_name ?: 'Anonim',
                    'customer_email' => $donation->donor_email ?: 'anonymous@donasi.com',
                    'customer_phone' => $donation->donor_whatsapp ?: '08123456789',
                    'item_name' => $campaign->title,
                    'donation_id' => $donation->id,
                ];
                
                \Log::info('=== TRIPAY TRANSACTION START ===');
                \Log::info('Donation ID: ' . $donation->id);
                \Log::info('Payment Method: ' . $vaCode);
                \Log::info('Merchant Ref: ' . $merchantRef);
                \Log::info('Amount: ' . $donation->amount);
                \Log::info('Creating Tripay transaction with data: ' . json_encode($tripayData));
                
                $tripayResult = $tripayService->createTransaction($tripayData);
                \Log::info('Tripay API Response: ' . json_encode($tripayResult));
                
                if ($tripayResult && isset($tripayResult['success']) && $tripayResult['success']) {
                    $updateData = [
                        'tripay_reference' => $tripayResult['data']['reference'] ?? null,
                        'tripay_fee' => $tripayResult['data']['total_fee'] ?? null,
                        'tripay_status' => $tripayResult['data']['status'] ?? null,
                        'tripay_payment_url' => $tripayResult['data']['checkout_url'] ?? null,
                        'payment_status' => 'pending',
                    ];
                    
                    \Log::info('Tripay transaction SUCCESS');
                    \Log::info('Reference: ' . ($tripayResult['data']['reference'] ?? 'NULL'));
                    \Log::info('Pay Code: ' . ($tripayResult['data']['pay_code'] ?? 'NULL'));
                    \Log::info('Status: ' . ($tripayResult['data']['status'] ?? 'NULL'));
                    \Log::info('Updating donation with: ' . json_encode($updateData));
                    
                    // Update donation dengan logging detail
                    try {
                        $updateResult = $donation->update($updateData);
                        \Log::info('Update result: ' . ($updateResult ? 'SUCCESS' : 'FAILED'));
                        
                        if ($updateResult) {
                            // Force refresh dari database
                            $donation->refresh();
                            \Log::info('Donation refreshed. Tripay reference: ' . ($donation->tripay_reference ?: 'NULL/EMPTY'));
                            \Log::info('Donation tripay_status: ' . ($donation->tripay_status ?: 'NULL'));
                            \Log::info('Donation tripay_fee: ' . ($donation->tripay_fee ?: 'NULL'));
                            
                            DB::commit();
                            \Log::info('=== TRIPAY TRANSACTION SUCCESS ===');
                            
                                        // Kirim pesan WhatsApp konfirmasi donasi untuk Tripay
            try {
                $whatsappService = new WhatsAppService();
                $whatsappService->sendDonationNotification($donation);
                \Log::info('WhatsApp confirmation sent for Tripay donation #' . $donation->id);
            } catch (\Exception $e) {
                \Log::error('Failed to send WhatsApp confirmation for Tripay donation #' . $donation->id . ': ' . $e->getMessage());
                // Jangan gagalkan proses donasi jika WhatsApp gagal
            }

            // Track analytics event
            try {
                $analyticsService = new \App\Services\AnalyticsService();
                $analyticsService->trackDonation($donation);
            } catch (\Exception $e) {
                \Log::error('Failed to track analytics for donation #' . $donation->id . ': ' . $e->getMessage());
            }
                            
                            // Redirect ke halaman pembayaran aplikasi dengan data VA
                            return redirect()->route('donations.payment', $donation->id)
                                           ->with('success', 'Donasi berhasil dibuat. Silakan selesaikan pembayaran.');
                        } else {
                            \Log::error('Update failed but no exception thrown');
                            DB::rollBack();
                            return back()->withInput()->withErrors(['error' => 'Gagal menyimpan data transaksi. Silakan coba lagi.']);
                        }
                    } catch (\Exception $e) {
                        \Log::error('Exception during update: ' . $e->getMessage());
                        \Log::error('Stack trace: ' . $e->getTraceAsString());
                        DB::rollBack();
                        return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.']);
                    }
                } else {
                    \Log::error('=== TRIPAY TRANSACTION FAILED ===');
                    \Log::error('Failed to create Tripay transaction: ' . json_encode($tripayResult));
                    DB::rollBack();
                    return back()->withInput()->withErrors(['error' => 'Gagal membuat transaksi Tripay. Silakan coba lagi.']);
                }
            }

            // Update campaign current amount
            $campaign->increment('current_amount', $request->amount);

            DB::commit();

            // Kirim pesan WhatsApp konfirmasi donasi
            try {
                $whatsappService = new WhatsAppService();
                $whatsappService->sendDonationNotification($donation);
                \Log::info('WhatsApp confirmation sent for donation #' . $donation->id);
            } catch (\Exception $e) {
                \Log::error('Failed to send WhatsApp confirmation for donation #' . $donation->id . ': ' . $e->getMessage());
                // Jangan gagalkan proses donasi jika WhatsApp gagal
            }

            // Track analytics event
            try {
                $analyticsService = new \App\Services\AnalyticsService();
                $analyticsService->trackDonation($donation);
            } catch (\Exception $e) {
                \Log::error('Failed to track analytics for donation #' . $donation->id . ': ' . $e->getMessage());
            }

            // Redirect to payment page
            return redirect()->route('donations.payment', $donation->id)
                           ->with('success', 'Donasi berhasil dibuat. Silakan selesaikan pembayaran.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan saat memproses donasi.']);
        }
    }

    public function payment(Donation $donation)
    {
        // Get active bank accounts
        $bankAccounts = \App\Models\BankAccount::active()->get();
        
        // Jika menggunakan Tripay, ambil detail transaksi dan instruksi
        $tripayData = null;
        $paymentInstructions = null;
        
        if (str_starts_with($donation->payment_method, 'tripay_')) {
            $tripayService = new TripayService();
            
            // Debug: Log informasi donasi
            \Log::info('=== PAYMENT PAGE DEBUG ===');
            \Log::info('Donation ID: ' . $donation->id);
            \Log::info('Payment method: ' . $donation->payment_method);
            \Log::info('Tripay reference: ' . ($donation->tripay_reference ?: 'NULL/EMPTY'));
            \Log::info('Tripay status: ' . ($donation->tripay_status ?: 'NULL'));
            
            if ($donation->tripay_reference) {
                \Log::info('Fetching Tripay transaction detail for reference: ' . $donation->tripay_reference);
                $tripayData = $tripayService->getTransactionDetail($donation->tripay_reference);
                \Log::info('Tripay detail response: ' . json_encode($tripayData));
                
                if ($tripayData && isset($tripayData['success']) && $tripayData['success']) {
                    \Log::info('Tripay detail SUCCESS');
                    \Log::info('Pay Code: ' . ($tripayData['data']['pay_code'] ?? 'NULL'));
                    \Log::info('Status: ' . ($tripayData['data']['status'] ?? 'NULL'));
                    
                    // Gunakan instruksi dari response transaksi Tripay (sudah ada kode VA yang benar)
                    if (isset($tripayData['data']['instructions']) && !empty($tripayData['data']['instructions'])) {
                        $paymentInstructions = [
                            'success' => true,
                            'data' => $tripayData['data']['instructions']
                        ];
                        \Log::info('Using instructions from Tripay transaction response');
                    }
                } else {
                    \Log::error('Tripay detail FAILED or invalid response');
                }
            } else {
                \Log::warning('Tripay reference tidak ditemukan untuk donasi ID: ' . $donation->id);
                \Log::warning('Ini berarti transaksi Tripay gagal dibuat atau tidak tersimpan dengan benar');
            }
            
            // Hanya ambil instruksi dari API terpisah jika tidak ada dari response transaksi
            if (!$paymentInstructions) {
                $vaCode = strtoupper(str_replace('tripay_', '', $donation->payment_method));
                \Log::info('Fetching payment instructions from separate API for: ' . $vaCode);
                $paymentInstructions = $tripayService->getPaymentInstructions($vaCode);
                \Log::info('Payment instructions response: ' . json_encode($paymentInstructions));
            }
            \Log::info('=== PAYMENT PAGE DEBUG END ===');
        }
        
        return view('donations.payment', compact('donation', 'bankAccounts', 'tripayData', 'paymentInstructions'));
    }

    public function success(Donation $donation)
    {
        return view('donations.success', compact('donation'));
    }

    public function uploadProof(Request $request, Donation $donation)
    {
        $request->validate([
            'payment_proof' => 'required|file|mimes:jpeg,jpg,png,pdf|max:2048',
            'notes' => 'nullable|string|max:1000',
        ], [
            'payment_proof.required' => 'Bukti pembayaran harus diupload.',
            'payment_proof.file' => 'File bukti pembayaran tidak valid.',
            'payment_proof.mimes' => 'Format file harus JPG, PNG, atau PDF.',
            'payment_proof.max' => 'Ukuran file maksimal 2MB.',
        ]);

        try {
            // Handle file upload
            if ($request->hasFile('payment_proof')) {
                $file = $request->file('payment_proof');
                $fileName = 'payment_proof_' . $donation->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                
                // Store file in storage/app/public/payment_proofs
                $path = $file->storeAs('payment_proofs', $fileName, 'public');
                
                // Update donation record
                $donation->update([
                    'payment_proof' => $path,
                    'payment_notes' => $request->notes,
                    'payment_status' => 'waiting_confirmation',
                    'proof_uploaded_at' => now(),
                ]);

                return redirect()->route('donations.verification', $donation->id);
            }

            return back()->withErrors(['error' => 'Gagal mengupload file. Silakan coba lagi.']);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan saat mengupload bukti pembayaran.']);
        }
    }

    public function create(Campaign $campaign)
    {
        $bankAccounts = BankAccount::all();
        $tripayService = new TripayService();
        $tripayChannels = $tripayService->getEnabledPaymentMethods();
        
        return view('campaigns.donate', compact('campaign', 'bankAccounts', 'tripayChannels'));
    }
} 