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
        // Fix semua URL yang masih menggunakan domain development
        
        // 1. Fix campaign image URLs
        DB::table('campaigns')
            ->where('image_url', 'like', '%donasi-apps.test%')
            ->update([
                'image_url' => DB::raw("REPLACE(image_url, 'http://donasi-apps.test', '')")
            ]);

        // 2. Fix campaign descriptions yang mungkin berisi URL gambar editor
        DB::table('campaigns')
            ->where('description', 'like', '%donasi-apps.test%')
            ->update([
                'description' => DB::raw("REPLACE(description, 'http://donasi-apps.test', '')")
            ]);

        // 3. Fix slider images jika ada
        if (Schema::hasTable('sliders')) {
            DB::table('sliders')
                ->where('image', 'like', '%donasi-apps.test%')
                ->update([
                    'image' => DB::raw("REPLACE(image, 'http://donasi-apps.test/', '')")
                ]);
        }

        // 4. Fix app settings jika ada image URL
        if (Schema::hasTable('app_settings')) {
            DB::table('app_settings')
                ->where('value', 'like', '%donasi-apps.test%')
                ->update([
                    'value' => DB::raw("REPLACE(value, 'http://donasi-apps.test', '')")
                ]);
        }

        // 5. Fix donation messages yang mungkin berisi URL
        if (Schema::hasTable('donations')) {
            DB::table('donations')
                ->where('message', 'like', '%donasi-apps.test%')
                ->update([
                    'message' => DB::raw("REPLACE(message, 'http://donasi-apps.test', '')")
                ]);
        }

        // 6. Update semua tabel yang mungkin berisi URL development
        $tables = ['campaigns', 'sliders', 'app_settings', 'donations'];
        
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                // Get all text/varchar columns
                $columns = DB::select("SHOW COLUMNS FROM {$table} WHERE Type LIKE '%text%' OR Type LIKE '%varchar%'");
                
                foreach ($columns as $column) {
                    $columnName = $column->Field;
                    try {
                        DB::table($table)
                            ->where($columnName, 'like', '%donasi-apps.test%')
                            ->update([
                                $columnName => DB::raw("REPLACE({$columnName}, 'http://donasi-apps.test', '')")
                            ]);
                    } catch (\Exception $e) {
                        // Skip jika ada error (misalnya column tidak bisa diupdate)
                        continue;
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: Tidak disarankan untuk revert karena akan mengembalikan ke URL development
        // Jika perlu revert, bisa dilakukan manual sesuai kebutuhan
    }
}; 