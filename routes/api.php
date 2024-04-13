<?php

use App\Http\Controllers\TT2Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/tt2/raid_target', [TT2Controller::class, 'raidTargetCalc']);
