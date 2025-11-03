<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\Campaign;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    public function index(Request $request)
    {
        $query = Donation::with(['campaign', 'user']);

        // Filter by campaign
        if ($request->filled('campaign_id')) {
            $query->where('campaign_id', $request->campaign_id);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by donor email
        if ($request->filled('donor_email')) {
            $query->where('donor_email', 'like', '%' . $request->donor_email . '%');
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $donations = $query->latest()->paginate(15);
        $campaigns = Campaign::all();

        return view('admin.donations.index', compact('donations', 'campaigns'));
    }

    public function show(Donation $donation)
    {
        $donation->load(['campaign.category', 'user']);
        return view('admin.donations.show', compact('donation'));
    }

    public function updateStatus(Request $request, Donation $donation)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,waiting_confirmation,success,failed'
        ]);

        $oldStatus = $donation->payment_status;
        $donation->update([
            'payment_status' => $request->payment_status
        ]);

        // Kirim pesan WhatsApp jika status berubah menjadi success
        if ($request->payment_status === 'success' && $oldStatus !== 'success') {
            try {
                $whatsappService = new WhatsAppService();
                $whatsappService->sendDonationNotification($donation);
                \Log::info('WhatsApp payment success notification sent for donation #' . $donation->id);
            } catch (\Exception $e) {
                \Log::error('Failed to send WhatsApp payment success notification for donation #' . $donation->id . ': ' . $e->getMessage());
                // Jangan gagalkan proses konfirmasi jika WhatsApp gagal
            }
        }

        return redirect()->route('admin.donations.show', $donation)
                        ->with('success', 'Status pembayaran berhasil diperbarui!');
    }

    public function confirm(Donation $donation)
    {
        try {
            $donation->update([
                'payment_status' => 'success'
            ]);

            // Kirim pesan WhatsApp konfirmasi pembayaran berhasil
            try {
                $whatsappService = new WhatsAppService();
                $whatsappService->sendDonationNotification($donation);
                \Log::info('WhatsApp payment success notification sent for donation #' . $donation->id);
            } catch (\Exception $e) {
                \Log::error('Failed to send WhatsApp payment success notification for donation #' . $donation->id . ': ' . $e->getMessage());
                // Jangan gagalkan proses konfirmasi jika WhatsApp gagal
            }

            return response()->json([
                'success' => true,
                'message' => 'Donasi berhasil dikonfirmasi!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengkonfirmasi donasi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reject(Request $request, Donation $donation)
    {
        try {
            $donation->update([
                'payment_status' => 'failed'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Donasi berhasil ditolak!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak donasi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export(Request $request)
    {
        $query = Donation::with(['campaign', 'user']);

        // Apply filters
        if ($request->filled('campaign_id')) {
            $query->where('campaign_id', $request->campaign_id);
        }
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $donations = $query->get();

        // Generate CSV
        $filename = 'donations_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($donations) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'ID', 'Campaign', 'Donor Name', 'Donor Email', 'Amount', 
                'Payment Method', 'Payment Status', 'Message', 'Anonymous', 'Created At'
            ]);

            // CSV data
            foreach ($donations as $donation) {
                fputcsv($file, [
                    $donation->id,
                    $donation->campaign->title,
                    $donation->donor_name,
                    $donation->donor_email,
                    $donation->amount,
                    $donation->payment_method,
                    $donation->payment_status,
                    $donation->message,
                    $donation->is_anonymous ? 'Yes' : 'No',
                    $donation->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}