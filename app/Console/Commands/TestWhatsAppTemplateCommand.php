<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Donation;
use App\Services\WhatsAppService;

class TestWhatsAppTemplateCommand extends Command
{
    protected $signature = 'test:wa-template {donation_id}';
    protected $description = 'Test kirim notifikasi WhatsApp dengan template dari database untuk donasi tertentu';

    public function handle()
    {
        $donationId = $this->argument('donation_id');
        $donation = Donation::with('campaign', 'user')->find($donationId);
        if (!$donation) {
            $this->error('Donasi tidak ditemukan!');
            return 1;
        }
        $waService = new WhatsAppService();
        $result = $waService->sendDonationNotification($donation);
        $this->info('Hasil pengiriman notifikasi WhatsApp:');
        $this->line(print_r($result, true));
        return 0;
    }
} 