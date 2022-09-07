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

//$PATH_MC='App\Http\Controllers\EntityController';

Route::get('/', function () {
    return view('welcome');
});

Route::get('/create/refresh/{code}', 'TokenController@createRefreshByCode');
Route::get('/change/refresh/{code}', 'TokenController@changeRefreshByCode');
Route::get('/refresh', 'TokenController@refresh');
Route::get('/load', 'EntityController@load');
