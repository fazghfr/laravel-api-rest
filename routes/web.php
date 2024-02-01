<?php

use App\Http\Controllers\pageController;
use Illuminate\Support\Facades\Route;

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

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;


Route::get('/', [pageController::class, 'home_page']);

//login and register routes
Route::get('/login', [pageController::class, 'login_page']);
Route::get('/register', [pageController::class, 'register_page']);
Route::post('/register', [pageController::class, 'register']);
Route::get('/logout', [pageController::class, 'log_out']); //buat sementara
Route::post('/login', [pageController::class, 'login']);

