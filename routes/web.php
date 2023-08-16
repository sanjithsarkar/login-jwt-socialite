<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Route::middleware(['auth:api', 'admin'])->group(function () {
//     Route::post('admin/register', 'AdminAuthController@register');
//     Route::post('admin/login', 'AdminAuthController@login');
// });
Route::get('/login', [AuthController::class, 'viewLogin'])->name('login');
Route::get('/register', [AuthController::class, 'viewRegister'])->name('register');
Route::post('/user/register', [AuthController::class, 'register'])->name('user.register');
Route::post('/user/login', [AuthController::class, 'login'])->name('user.login');
Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

Route::get('auth/google', [AuthController::class, 'signInwithGoogle']);
Route::get('callback/google', [AuthController::class, 'callbackToGoogle']);

