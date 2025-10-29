<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Create default admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@donasi.com',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
            'role' => 'admin',
        ]);

        $this->call([
            AppSettingSeeder::class,
            CategorySeeder::class,
            CampaignSeeder::class,
            SliderSeeder::class,
            BankAccountSeeder::class,
            PrayerSeeder::class,
        ]);
    }
}
