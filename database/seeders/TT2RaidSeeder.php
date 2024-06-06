<?php

namespace Database\Seeders;

use App\Models\TT2Raid;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TT2RaidSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TT2Raid::factory(10)->create();
    }
}
