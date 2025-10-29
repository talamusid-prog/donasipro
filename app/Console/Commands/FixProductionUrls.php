<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixProductionUrls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:production-urls {--dry-run : Show what would be changed without actually changing it}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix URLs dari development ke production untuk mengatasi Mixed Content issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $developmentUrl = 'http://donasi-apps.test';
        
        if ($dryRun) {
            $this->info('🔍 DRY RUN MODE - Tidak ada perubahan yang akan disimpan');
            $this->line('');
        } else {
            $this->info('🚀 Memulai proses fix URLs production...');
        }

        $totalChanges = 0;

        // 1. Fix campaign image URLs
        $this->info('📸 Memeriksa campaign image URLs...');
        $campaignImages = DB::table('campaigns')
            ->where('image_url', 'like', "%{$developmentUrl}%")
            ->get(['id', 'title', 'image_url']);

        if ($campaignImages->count() > 0) {
            $this->table(['ID', 'Title', 'Current URL'], 
                $campaignImages->map(fn($item) => [$item->id, $item->title, $item->image_url])->toArray()
            );

            if (!$dryRun) {
                $affected = DB::table('campaigns')
                    ->where('image_url', 'like', "%{$developmentUrl}%")
                    ->update(['image_url' => DB::raw("REPLACE(image_url, '{$developmentUrl}', '')")]);
                $this->info("✅ Updated {$affected} campaign image URLs");
                $totalChanges += $affected;
            }
        } else {
            $this->line('✅ Tidak ada campaign image URLs yang perlu diperbaiki');
        }

        // 2. Fix campaign descriptions
        $this->info('📝 Memeriksa campaign descriptions...');
        $campaignDescriptions = DB::table('campaigns')
            ->where('description', 'like', "%{$developmentUrl}%")
            ->get(['id', 'title']);

        if ($campaignDescriptions->count() > 0) {
            $this->table(['ID', 'Title'], 
                $campaignDescriptions->map(fn($item) => [$item->id, $item->title])->toArray()
            );

            if (!$dryRun) {
                $affected = DB::table('campaigns')
                    ->where('description', 'like', "%{$developmentUrl}%")
                    ->update(['description' => DB::raw("REPLACE(description, '{$developmentUrl}', '')")]);
                $this->info("✅ Updated {$affected} campaign descriptions");
                $totalChanges += $affected;
            }
        } else {
            $this->line('✅ Tidak ada campaign descriptions yang perlu diperbaiki');
        }

        // 3. Fix slider images
        if (Schema::hasTable('sliders')) {
            $this->info('🖼️ Memeriksa slider images...');
            $sliderImages = DB::table('sliders')
                ->where('image', 'like', "%{$developmentUrl}%")
                ->get(['id', 'title', 'image']);

            if ($sliderImages->count() > 0) {
                $this->table(['ID', 'Title', 'Current Image'], 
                    $sliderImages->map(fn($item) => [$item->id, $item->title, $item->image])->toArray()
                );

                if (!$dryRun) {
                    $affected = DB::table('sliders')
                        ->where('image', 'like', "%{$developmentUrl}%")
                        ->update(['image' => DB::raw("REPLACE(image, '{$developmentUrl}/', '')")]);
                    $this->info("✅ Updated {$affected} slider images");
                    $totalChanges += $affected;
                }
            } else {
                $this->line('✅ Tidak ada slider images yang perlu diperbaiki');
            }
        }

        // 4. Fix app settings
        if (Schema::hasTable('app_settings')) {
            $this->info('⚙️ Memeriksa app settings...');
            $appSettings = DB::table('app_settings')
                ->where('value', 'like', "%{$developmentUrl}%")
                ->get(['id', 'key', 'value']);

            if ($appSettings->count() > 0) {
                $this->table(['ID', 'Key', 'Current Value'], 
                    $appSettings->map(fn($item) => [$item->id, $item->key, $item->value])->toArray()
                );

                if (!$dryRun) {
                    $affected = DB::table('app_settings')
                        ->where('value', 'like', "%{$developmentUrl}%")
                        ->update(['value' => DB::raw("REPLACE(value, '{$developmentUrl}', '')")]);
                    $this->info("✅ Updated {$affected} app settings");
                    $totalChanges += $affected;
                }
            } else {
                $this->line('✅ Tidak ada app settings yang perlu diperbaiki');
            }
        }

        $this->line('');
        if ($dryRun) {
            $this->info('🔍 DRY RUN selesai. Jalankan tanpa --dry-run untuk menerapkan perubahan.');
        } else {
            $this->info("🎉 Fix URLs selesai! Total {$totalChanges} record berhasil diperbaiki.");
            $this->line('');
            $this->comment('💡 Tips: Jalankan php artisan cache:clear untuk memastikan perubahan diterapkan.');
        }

        return 0;
    }
} 