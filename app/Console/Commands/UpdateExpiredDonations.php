<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Donation;
use App\Services\WhatsAppService;

class UpdateExpiredDonations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'donations:update-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update expired donations status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating expired donations...');

        // Ambil donasi yang sudah expired dan masih pending
        $expiredDonations = Donation::where('payment_status', 'pending')
            ->where('expired_at', '<=', now())
            ->get();

        $this->info("Found {$expiredDonations->count()} expired donations");

        $updatedCount = 0;

        foreach ($expiredDonations as $donation) {
            try {
                $donation->update(['payment_status' => 'expired']);
                $updatedCount++;
                $this->info("✓ Updated donation #{$donation->id} to expired");
            } catch (\Exception $e) {
                $this->error("✗ Error updating donation #{$donation->id}: " . $e->getMessage());
            }
        }

        $this->info("Expired donations update completed: {$updatedCount} updated");

        return 0;
    }
} 