<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Donation;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;

class SendPaymentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'donations:send-reminders {--hours=12 : Hours before expiry to send reminder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send payment reminders for pending donations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = $this->option('hours');
        $this->info("Sending payment reminders for donations expiring in {$hours} hours...");

        // Get donations that are pending and expiring soon
        $donations = Donation::where('payment_status', 'pending')
                            ->where('expired_at', '>', now())
                            ->where('expired_at', '<=', now()->addHours($hours))
                            ->whereNotNull('donor_whatsapp')
                            ->get();

        $this->info("Found {$donations->count()} donations to send reminders for.");

        $successCount = 0;
        $errorCount = 0;

        foreach ($donations as $donation) {
            try {
                $whatsappService = new WhatsAppService();
                $result = $whatsappService->sendPaymentReminder($donation);

                if ($result && isset($result['success']) && $result['success']) {
                    $successCount++;
                    $this->info("✓ Reminder sent for donation #{$donation->id} to {$donation->donor_whatsapp}");
                    Log::info("Payment reminder sent for donation #{$donation->id}");
                } else {
                    $errorCount++;
                    $this->error("✗ Failed to send reminder for donation #{$donation->id}: " . ($result['message'] ?? 'Unknown error'));
                    Log::error("Failed to send payment reminder for donation #{$donation->id}: " . json_encode($result));
                }
            } catch (\Exception $e) {
                $errorCount++;
                $this->error("✗ Exception for donation #{$donation->id}: " . $e->getMessage());
                Log::error("Exception sending payment reminder for donation #{$donation->id}: " . $e->getMessage());
            }
        }

        $this->info("\nSummary:");
        $this->info("✓ Successfully sent: {$successCount}");
        $this->info("✗ Failed: {$errorCount}");

        return 0;
    }
}
