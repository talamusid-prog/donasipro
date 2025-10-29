<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class SyncStorageFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:sync {--force : Force overwrite existing files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync files dari storage/app/public ke public/storage untuk memastikan file tersedia';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $force = $this->option('force');
        $this->info('🔄 Memulai sinkronisasi file storage...');
        
        $sourceBase = storage_path('app/public');
        $destBase = public_path('storage');
        
        // Folders yang perlu disync
        $folders = [
            'editor-images',
            'campaigns', 
            'sliders',
            'payment_proofs',
            'settings'
        ];
        
        $totalSynced = 0;
        
        foreach ($folders as $folder) {
            $sourcePath = $sourceBase . DIRECTORY_SEPARATOR . $folder;
            $destPath = $destBase . DIRECTORY_SEPARATOR . $folder;
            
            if (!is_dir($sourcePath)) {
                $this->line("⏭️  Folder {$folder} tidak ditemukan di storage, skip...");
                continue;
            }
            
            $this->info("📁 Memproses folder: {$folder}");
            
            // Pastikan folder tujuan ada
            if (!is_dir($destPath)) {
                mkdir($destPath, 0755, true);
                $this->line("  ✅ Folder tujuan dibuat: {$destPath}");
            }
            
            // Get semua file di source folder
            $files = File::files($sourcePath);
            
            foreach ($files as $file) {
                $fileName = $file->getFilename();
                $sourceFile = $sourcePath . DIRECTORY_SEPARATOR . $fileName;
                $destFile = $destPath . DIRECTORY_SEPARATOR . $fileName;
                
                $shouldCopy = false;
                
                if (!file_exists($destFile)) {
                    $shouldCopy = true;
                    $action = 'BARU';
                } elseif ($force || filemtime($sourceFile) > filemtime($destFile)) {
                    $shouldCopy = true;
                    $action = 'UPDATE';
                } else {
                    $action = 'SKIP';
                }
                
                if ($shouldCopy) {
                    if (copy($sourceFile, $destFile)) {
                        $this->line("  ✅ {$action}: {$fileName}");
                        $totalSynced++;
                    } else {
                        $this->error("  ❌ GAGAL: {$fileName}");
                    }
                } else {
                    $this->line("  ⏭️  {$action}: {$fileName}");
                }
            }
        }
        
        $this->line('');
        $this->info("🎉 Sinkronisasi selesai! Total {$totalSynced} file berhasil disinkronkan.");
        
        // Cek symbolic link
        $this->line('');
        $this->info('🔗 Memeriksa symbolic link...');
        
        $storageLink = public_path('storage');
        if (is_link($storageLink)) {
            $this->info('✅ Symbolic link sudah ada dan valid');
        } elseif (is_dir($storageLink)) {
            $this->info('✅ Folder storage sudah ada (bukan symbolic link, tapi itu OK untuk production)');
        } else {
            $this->warn('⚠️  Folder storage tidak ditemukan, membuat symbolic link...');
            if (PHP_OS_FAMILY === 'Windows') {
                $this->info('💡 Untuk Windows, gunakan: mklink /D public\\storage storage\\app\\public');
            } else {
                $this->call('storage:link');
            }
        }
        
        return 0;
    }
} 