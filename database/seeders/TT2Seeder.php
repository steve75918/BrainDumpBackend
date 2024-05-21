<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TT2Seeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            TT2MemberSeeder::class,
            TT2RaidSeeder::class,
            TT2RaidStatisticSeeder::class,
        ]);
    }
}
