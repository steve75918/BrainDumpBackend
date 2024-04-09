<?php

use App\Http\Controllers\TT2Controller;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/tt2/raid_target', [TT2Controller::class, 'raidTargetCalc']);