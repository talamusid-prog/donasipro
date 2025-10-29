<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            // Tambah field untuk fitur amin
            $table->integer('amin_count')->default(0)->after('message');
            $table->json('amin_users')->nullable()->after('amin_count'); // Menyimpan ID user yang sudah amin
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn(['amin_count', 'amin_users']);
        });
    }
};
