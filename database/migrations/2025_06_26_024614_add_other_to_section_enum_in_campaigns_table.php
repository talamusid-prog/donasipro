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
        Schema::table('campaigns', function (Blueprint $table) {
            // Tambahkan 'other' ke enum section
            $table->enum('section', ['featured', 'urgent', 'popular', 'new', 'ending_soon', 'other'])->default('new')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            // Kembalikan ke enum tanpa 'other'
            $table->enum('section', ['featured', 'urgent', 'popular', 'new', 'ending_soon'])->default('new')->change();
        });
    }
};
