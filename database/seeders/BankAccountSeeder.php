<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BankAccount;

class BankAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BankAccount::create([
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'account_holder' => 'Donasi Apps',
            'description' => 'Rekening utama untuk menerima donasi',
            'is_active' => true,
        ]);

        BankAccount::create([
            'bank_name' => 'Mandiri',
            'account_number' => '0987654321',
            'account_holder' => 'Donasi Apps',
            'description' => 'Rekening alternatif untuk menerima donasi',
            'is_active' => true,
        ]);
    }
}
