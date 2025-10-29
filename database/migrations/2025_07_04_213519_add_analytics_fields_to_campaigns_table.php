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
            // UTM Parameters untuk tracking
            $table->string('utm_source')->nullable()->after('is_verified');
            $table->string('utm_medium')->nullable()->after('utm_source');
            $table->string('utm_campaign')->nullable()->after('utm_medium');
            
            // Analytics tracking flags
            $table->boolean('track_conversions')->default(true)->after('utm_campaign');
            $table->boolean('track_engagement')->default(true)->after('track_conversions');
            $table->boolean('enhanced_ecommerce')->default(true)->after('track_engagement');
            
            // Analytics metadata
            $table->json('analytics_metadata')->nullable()->after('enhanced_ecommerce');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn([
                'utm_source',
                'utm_medium', 
                'utm_campaign',
                'track_conversions',
                'track_engagement',
                'enhanced_ecommerce',
                'analytics_metadata'
            ]);
        });
    }
};
