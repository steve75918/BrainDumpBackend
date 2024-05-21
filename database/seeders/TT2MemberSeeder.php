<?php

namespace Database\Seeders;

use App\Models\TT2Member;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TT2MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TT2Member::factory(5)->create();
    }
}
