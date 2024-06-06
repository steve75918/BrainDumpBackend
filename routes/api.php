<?php

use App\Http\Controllers\TT2Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('tt2')->group(function () {
    Route::post('/raid_target', [TT2Controller::class, 'raidTargetCalc']);
    Route::post('/upload', [TT2Controller::class, 'uploadRaidReport']);
    Route::get('/raid_attendance', [TT2Controller::class, 'raidAttendance']);
});

