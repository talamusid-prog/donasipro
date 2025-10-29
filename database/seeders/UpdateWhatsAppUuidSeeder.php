<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateWhatsAppUuidSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update UUID session WA Blast
        DB::table('app_settings')
            ->where('key', 'wa_blast_session_uuid')
            ->update([
                'value' => '7d549d3d-a951-478e-b4d7-c90e465bd706',
                'updated_at' => now()
            ]);

        echo "✅ UUID Session WA Blast berhasil diupdate ke: 7d549d3d-a951-478e-b4d7-c90e465bd706\n";
        
        // Tampilkan setting yang diupdate
        $setting = DB::table('app_settings')
            ->where('key', 'wa_blast_session_uuid')
            ->first();
            
        if ($setting) {
            echo "📋 Detail Setting:\n";
            echo "   Key: {$setting->key}\n";
            echo "   Value: {$setting->value}\n";
            echo "   Label: {$setting->label}\n";
            echo "   Type: {$setting->type}\n";
            echo "   Group: {$setting->group}\n";
        }
    }
}
