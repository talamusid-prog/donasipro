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
            $table->string('tripay_reference')->nullable()->after('payment_status');
            $table->integer('tripay_fee')->nullable()->after('tripay_reference');
            $table->string('tripay_status')->nullable()->after('tripay_fee');
            $table->string('tripay_payment_url')->nullable()->after('tripay_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            //
        });
    }
};
