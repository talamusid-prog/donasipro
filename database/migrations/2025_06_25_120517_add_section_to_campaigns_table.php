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
            // Tambah field section untuk menentukan di section mana campaign ditampilkan
            $table->enum('section', ['featured', 'urgent', 'popular', 'new', 'ending_soon'])->default('new')->after('category');
            
            // Perluas kategori campaign dengan kategori yang lebih lengkap
            $table->enum('category', [
                'yatim-dhuafa',      // Yatim & Dhuafa
                'medical',           // Bantuan Medis
                'education',         // Pendidikan
                'mosque',            // Masjid
                'disaster',          // Bencana Alam
                'orphanage',         // Panti Asuhan
                'hospital',          // Rumah Sakit
                'school',            // Sekolah
                'community',         // Masyarakat
                'environment',       // Lingkungan
                'animal',            // Hewan
                'other'              // Lainnya
            ])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn('section');
            
            // Kembalikan kategori ke versi sebelumnya
            $table->enum('category', ['yatim-dhuafa', 'medical', 'education', 'mosque'])->change();
        });
    }
};
