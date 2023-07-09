<?php
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;

Route::post('/upload/images',[Controller::class,'uploadImages']);