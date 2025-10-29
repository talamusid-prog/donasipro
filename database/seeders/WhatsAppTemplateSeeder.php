<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\WhatsAppTemplate;

class WhatsAppTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Konfirmasi Donasi',
                'type' => 'donation_confirmation',
                'title' => '🎉 Terima Kasih Atas Donasi Anda!',
                'template' => "Halo {donor_name},\n\nTerima kasih telah berdonasi untuk:\n*{campaign_title}*\n\n📋 Detail Donasi:\n• Nominal: Rp {amount}\n• Metode Pembayaran: {payment_method}\n• Status: {payment_status}\n• ID Donasi: #{donation_id}\n\n🏦 Informasi Rekening:\n{bank_info}\n\n⏰ Batas Waktu Pembayaran:\n{expired_at}\n\n🔗 Link Pembayaran:\n{payment_url}\n\nJika ada pertanyaan, silakan hubungi kami.\nTerima kasih atas kebaikan hati Anda! 🙏",
                'variables' => [
                    'donor_name' => 'Nama donatur',
                    'campaign_title' => 'Judul kampanye',
                    'amount' => 'Nominal donasi (format: 1.000.000)',
                    'payment_method' => 'Metode pembayaran',
                    'payment_status' => 'Status pembayaran',
                    'donation_id' => 'ID donasi',
                    'bank_info' => 'Informasi rekening bank (nama bank, nomor rekening, atas nama)',
                    'expired_at' => 'Batas waktu pembayaran',
                    'payment_url' => 'URL halaman pembayaran'
                ],
                'description' => 'Pesan yang dikirim setelah user melakukan donasi'
            ],
            [
                'name' => 'Reminder Pembayaran',
                'type' => 'payment_reminder',
                'title' => '⏰ Reminder Pembayaran Donasi',
                'template' => "Halo {donor_name},\n\nDonasi Anda untuk *{campaign_title}* belum diselesaikan.\n\n📋 Detail Donasi:\n• Nominal: Rp {amount}\n• Sisa waktu: {hours_left} jam\n• ID Donasi: #{donation_id}\n\n🔗 Link Pembayaran:\n{payment_url}\n\nSilakan selesaikan pembayaran sebelum batas waktu.\nTerima kasih! 🙏",
                'variables' => [
                    'donor_name' => 'Nama donatur',
                    'campaign_title' => 'Judul kampanye',
                    'amount' => 'Nominal donasi (format: 1.000.000)',
                    'hours_left' => 'Sisa waktu dalam jam',
                    'donation_id' => 'ID donasi',
                    'payment_url' => 'URL halaman pembayaran'
                ],
                'description' => 'Pesan reminder untuk pembayaran yang belum diselesaikan'
            ],
            [
                'name' => 'Konfirmasi Pembayaran Berhasil',
                'type' => 'payment_success',
                'title' => '✅ Pembayaran Berhasil Dikonfirmasi!',
                'template' => "Halo {donor_name},\n\nPembayaran donasi Anda telah berhasil dikonfirmasi!\n\n📋 Detail Donasi:\n• Kampanye: {campaign_title}\n• Nominal: Rp {amount}\n• ID Donasi: #{donation_id}\n• Status: Dikonfirmasi\n\n🎉 Terima kasih atas donasi Anda!\nDoa dan dukungan Anda sangat berarti bagi kami.\n\nSemoga Allah SWT membalas kebaikan Anda dengan berlipat ganda. Aamiin 🙏",
                'variables' => [
                    'donor_name' => 'Nama donatur',
                    'campaign_title' => 'Judul kampanye',
                    'amount' => 'Nominal donasi (format: 1.000.000)',
                    'donation_id' => 'ID donasi'
                ],
                'description' => 'Pesan yang dikirim setelah pembayaran berhasil dikonfirmasi'
            ]
        ];

        foreach ($templates as $template) {
            WhatsAppTemplate::updateOrCreate(
                ['name' => $template['name']],
                $template
            );
        }
    }
}
