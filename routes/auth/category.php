<?php
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::post('/category/store',[CategoryController::class, 'store']);
Route::post('/category/get',[CategoryController::class, 'get']);
Route::post('/category/update',[CategoryController::class, 'update']);
Route::post('/category/delete',[CategoryController::class, 'delete']);