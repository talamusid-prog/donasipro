<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Campaign;
use Illuminate\Support\Str;

class SlugCampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $campaigns = Campaign::all();
        foreach ($campaigns as $campaign) {
            $campaign->slug = Str::slug($campaign->title);
            $campaign->save();
        }
    }
}
