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
        // Update campaign image URLs from absolute to relative paths
        DB::table('campaigns')
            ->where('image_url', 'like', '%donasi-apps.test%')
            ->update([
                'image_url' => DB::raw("REPLACE(image_url, 'http://donasi-apps.test', '')")
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to absolute URLs if needed
        DB::table('campaigns')
            ->where('image_url', 'like', '/storage%')
            ->update([
                'image_url' => DB::raw("CONCAT('http://donasi-apps.test', image_url)")
            ]);
    }
};
