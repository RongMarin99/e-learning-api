<?php
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/login',[AuthController::class, 'login']);
Route::post('/register',[AuthController::class, 'register']);
Route::get('/login',function(){
    return response()->json([
        'message' => 'Unauthorized'
    ]);
})->name('login');

Route::group(['middleware'=>'auth:api'], function(){
    Route::post('getUser', [AuthController::class, 'getUser']);
    include('auth/auth.php');
});

Route::post('/requestResetPassword',[AuthController::class, 'requestResetPassword']);
Route::post('/confirmResetPassword',[AuthController::class,'confirmResetPassword']);
Route::post('/push_notification',[AuthController::class,'pushNotification']);
Route::post('/user/update_fcm_token',[AuthController::class,'updateRefreshToken']);
