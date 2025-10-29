<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Donation;
use App\Models\Campaign;

class PrayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil campaign yang ada
        $campaigns = Campaign::take(3)->get();
        
        if ($campaigns->count() == 0) {
            return; // Skip jika tidak ada campaign
        }

        $prayers = [
            [
                'donor_name' => 'Siti Aminah',
                'donor_email' => 'siti.aminah@example.com',
                'donor_whatsapp' => '+6281234567890',
                'message' => 'Semoga Allah SWT membalas kebaikan semua donatur dengan berlipat ganda. Aamiin.',
                'amount' => 50000,
                'payment_method' => 'bank_transfer',
                'payment_status' => 'success',
                'is_anonymous' => false,
            ],
            [
                'donor_name' => 'Budi Santoso',
                'donor_email' => 'budi.santoso@example.com',
                'donor_whatsapp' => '+6281234567891',
                'message' => 'Semoga bantuan ini bisa meringankan beban mereka yang membutuhkan. Semoga Allah SWT selalu melindungi kita semua.',
                'amount' => 100000,
                'payment_method' => 'bank_transfer',
                'payment_status' => 'success',
                'is_anonymous' => false,
            ],
            [
                'donor_name' => 'Rina Wijaya',
                'donor_email' => 'rina.wijaya@example.com',
                'donor_whatsapp' => '+6281234567892',
                'message' => 'Doa terbaik untuk semua yang terlibat dalam kebaikan ini. Semoga menjadi amal jariyah yang terus mengalir.',
                'amount' => 75000,
                'payment_method' => 'bank_transfer',
                'payment_status' => 'success',
                'is_anonymous' => false,
            ],
            [
                'donor_name' => 'Andi Pratama',
                'donor_email' => 'andi.pratama@example.com',
                'donor_whatsapp' => '+6281234567893',
                'message' => 'Semoga Allah SWT memberikan keberkahan pada setiap rupiah yang didonasikan. Aamiin ya rabbal alamin.',
                'amount' => 150000,
                'payment_method' => 'bank_transfer',
                'payment_status' => 'success',
                'is_anonymous' => false,
            ],
            [
                'donor_name' => 'Dewi Lestari',
                'donor_email' => 'dewi.lestari@example.com',
                'donor_whatsapp' => '+6281234567894',
                'message' => 'Doa untuk semua yang membutuhkan, semoga Allah SWT memberikan kemudahan dan pertolongan.',
                'amount' => 25000,
                'payment_method' => 'bank_transfer',
                'payment_status' => 'success',
                'is_anonymous' => false,
            ],
            [
                'donor_name' => 'Ahmad Hidayat',
                'donor_email' => 'ahmad.hidayat@example.com',
                'donor_whatsapp' => '+6281234567895',
                'message' => 'Semoga Allah SWT menerima amal ibadah kita semua dan memberikan pahala yang berlipat ganda.',
                'amount' => 200000,
                'payment_method' => 'bank_transfer',
                'payment_status' => 'success',
                'is_anonymous' => false,
            ],
            [
                'donor_name' => 'Nurul Hidayah',
                'donor_email' => 'nurul.hidayah@example.com',
                'donor_whatsapp' => '+6281234567896',
                'message' => 'Doa untuk semua yang terlibat dalam kebaikan ini. Semoga Allah SWT selalu memberikan kemudahan.',
                'amount' => 125000,
                'payment_method' => 'bank_transfer',
                'payment_status' => 'success',
                'is_anonymous' => false,
            ],
            [
                'donor_name' => 'Muhammad Rizki',
                'donor_email' => 'muhammad.rizki@example.com',
                'donor_whatsapp' => '+6281234567897',
                'message' => 'Semoga setiap rupiah yang didonasikan menjadi amal jariyah yang terus mengalir pahalanya.',
                'amount' => 300000,
                'payment_method' => 'bank_transfer',
                'payment_status' => 'success',
                'is_anonymous' => false,
            ],
            [
                'donor_name' => 'Fatimah Azzahra',
                'donor_email' => 'fatimah.azzahra@example.com',
                'donor_whatsapp' => '+6281234567898',
                'message' => 'Semoga Allah SWT memberikan keberkahan pada semua yang terlibat dalam kebaikan ini.',
                'amount' => 175000,
                'payment_method' => 'bank_transfer',
                'payment_status' => 'success',
                'is_anonymous' => false,
            ],
            [
                'donor_name' => 'Abdullah Rahman',
                'donor_email' => 'abdullah.rahman@example.com',
                'donor_whatsapp' => '+6281234567899',
                'message' => 'Doa terbaik untuk semua yang membutuhkan. Semoga Allah SWT memberikan pertolongan dan kemudahan.',
                'amount' => 80000,
                'payment_method' => 'bank_transfer',
                'payment_status' => 'success',
                'is_anonymous' => false,
            ],
        ];

        foreach ($prayers as $index => $prayer) {
            $campaign = $campaigns[$index % $campaigns->count()];
            
            Donation::create([
                'campaign_id' => $campaign->id,
                'donor_name' => $prayer['donor_name'],
                'donor_email' => $prayer['donor_email'],
                'donor_whatsapp' => $prayer['donor_whatsapp'],
                'message' => $prayer['message'],
                'amount' => $prayer['amount'],
                'payment_method' => $prayer['payment_method'],
                'payment_status' => $prayer['payment_status'],
                'is_anonymous' => $prayer['is_anonymous'],
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now()->subDays(rand(1, 30)),
            ]);
        }
    }
} 