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
        Schema::create('tripay_channels', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // BRIVA, BCAVA, etc
            $table->string('name'); // BRI Virtual Account, BCA Virtual Account, etc
            $table->string('group'); // Virtual Account, E-Wallet, etc
            $table->string('type')->default('direct');
            $table->string('icon_url')->nullable();
            $table->boolean('active')->default(false); // Channel aktif/nonaktif
            $table->boolean('is_enabled')->default(false); // Admin enable/disable
            $table->integer('fee_merchant_flat')->default(0);
            $table->decimal('fee_merchant_percent', 5, 2)->default(0);
            $table->integer('fee_customer_flat')->default(0);
            $table->decimal('fee_customer_percent', 5, 2)->default(0);
            $table->integer('total_fee_flat')->default(0);
            $table->decimal('total_fee_percent', 5, 2)->default(0);
            $table->integer('minimum_fee')->default(0);
            $table->integer('maximum_fee')->default(0);
            $table->integer('minimum_amount')->default(0);
            $table->integer('maximum_amount')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tripay_channels');
    }
};
