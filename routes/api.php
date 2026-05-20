<?php

use App\Http\Controllers\LinkController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/links', [LinkController::class, 'store']);
Route::get('/links', [LinkController::class, 'index']);
Route::delete('/links/{link}', [LinkController::class, 'destroy']);
