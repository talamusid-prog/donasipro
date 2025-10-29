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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('organization');
            $table->string('organization_logo')->nullable();
            $table->string('image_url')->nullable();
            $table->enum('category', ['yatim-dhuafa', 'medical', 'education', 'mosque']);
            $table->bigInteger('target_amount');
            $table->bigInteger('current_amount')->default(0);
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->enum('status', ['active', 'completed', 'expired'])->default('active');
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
}; 