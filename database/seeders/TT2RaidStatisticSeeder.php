<?php

namespace Database\Seeders;

use App\Models\TT2Member;
use App\Models\TT2Raid;
use App\Models\TT2RaidStatistic;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TT2RaidStatisticSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $raids = TT2Raid::all();
        TT2Member::all()->each(function ($member) use ($raids) {
            $raids->each(function ($raid) use ($member) {
                TT2RaidStatistic::factory()->create([
                    'member_id' => $member->id,
                    'raid_id'   => $raid->id,
                ]);
            });
        });
    }
}
