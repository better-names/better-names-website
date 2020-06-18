<?php

use Illuminate\Support\Facades\Route;

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

use App\Http\Responders\HandleGithubCallbackResponder;
use App\Http\Responders\ShowHomeResponder;

Route::get('/', [ShowHomeResponder::class, 'handle'])->name('show-home');
Route::get('/handle-github-callback', [HandleGithubCallbackResponder::class, 'handle'])->name('handle-github-callback');
