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
        Schema::create('tt2_raid_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('tt2_members');
            $table->foreignId('raid_id')->constrained('tt2_raids');
            $table->boolean('attendance');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tt2_raid_statistics');
    }
};
