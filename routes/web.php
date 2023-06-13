<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BlogController;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Response;


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

// Route::get('/', [SessionsController::class, 'create'])->name('home');
// Route::get('/blog', function () {
//     return view('blog');
// });

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/{url}', [BlogController::class, 'index']);
