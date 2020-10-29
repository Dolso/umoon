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
//Auth::routes(['verify' => true]);
Auth::routes();
Route::view('/', 'welcome');

Route::get('/home', 'HomeController@index')->name('home');

Route::post('/dev/{hash}', 'DevController@index');

Route::group(['middleware' => ['auth','verified']], function() {
    Route::resource('/bots', 'BotController');
});

Route::get('/home', 'HomeController@index')->name('home');