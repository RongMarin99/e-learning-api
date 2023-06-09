<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('account/reset/{token}', [AuthController::class, 'resetPassword'])->name('user.reset'); 

Route::get('account/verify/{id}', [AuthController::class, 'verifyAccount'])->name('user.verify'); 
//Route::post('account/verify', [AuthController::class, 'verifyAccount'])->name('user.verify'); 
