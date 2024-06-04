<?php

use Illuminate\Http\Request;
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

Route::get('/', function () {
    return view('welcome');
});

// handle API unauthenticated Fails
Route::get('api-login', function (Request $request) {
    return response()->json([
        'status' => 401,
        'data' => ['error' => trans('Unauthenticated!')]
    ]);
})->name('api.login');
