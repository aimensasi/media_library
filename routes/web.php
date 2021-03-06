<?php

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

Route::resource('explorers', 'FileExplorersController');

Route::patch('explorers/{explorer}/rename', 'FileExplorersController@rename')->name('explorers.rename');
Route::patch('explorers/{explorer}/move', 'FileExplorersController@move')->name('explorers.move');
