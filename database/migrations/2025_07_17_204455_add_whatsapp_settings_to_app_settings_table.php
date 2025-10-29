<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert WhatsApp settings into app_settings table
        DB::table('app_settings')->insert([
            [
                'key' => 'wa_blast_session_uuid',
                'value' => '7d549d3d-a951-478e-b4d7-c90e465bd706',
                'label' => 'UUID Session WA Blast',
                'description' => 'UUID session untuk koneksi WA Blast API',
                'type' => 'whatsapp_uuid',
                'group' => 'whatsapp',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'wa_blast_base_url',
                'value' => 'https://wa-blast.test',
                'label' => 'Base URL WA Blast',
                'description' => 'URL dasar untuk WA Blast API',
                'type' => 'text',
                'group' => 'whatsapp',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'wa_blast_api_key',
                'value' => 'wa-blast-live-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
                'label' => 'API Key WA Blast',
                'description' => 'API Key untuk autentikasi WA Blast API',
                'type' => 'text',
                'group' => 'whatsapp',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'wa_blast_enabled',
                'value' => '1',
                'label' => 'Aktifkan WA Blast',
                'description' => 'Aktifkan atau nonaktifkan integrasi WA Blast API',
                'type' => 'toggle',
                'group' => 'whatsapp',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove WhatsApp settings
        DB::table('app_settings')->whereIn('key', [
            'wa_blast_session_uuid',
            'wa_blast_base_url',
            'wa_blast_api_key',
            'wa_blast_enabled'
        ])->delete();
    }
};
