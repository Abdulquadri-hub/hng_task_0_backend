<?php

use App\Http\Controllers\StringAnalyzerController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::controller(UserController::class)->group(function () {
    Route::post('create', 'save');
    Route::get('me', 'profile');
});

Route::controller(StringAnalyzerController::class)->group(function (){ 
   Route::get('strings/filter-by-natural-language', 'fetchByNaturalLanguage');
   Route::post('strings', 'save');
   Route::get('strings', 'all');
   
   Route::get('strings/{string_value}', 'fetch');
   Route::delete('strings/{string_value}', 'destroy');
});