<?php
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('user',[AuthController::class, 'getUser']);
Route::post('/logout',[AuthController::class, 'logout']);
Route::post('user/update',[AuthController::class,'update']);
Route::post('user/founder',[AuthController::class,'founder']);