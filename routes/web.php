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

Route::get('/', 'FrontController@home')->name('home');
Route::view('/kullanim-kosullari.html', 'front.usage')->name('usage');
Route::view('/nasil-olusturulur.html', 'front.how')->name('how');
Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('/', 'AdminController@login');
    Auth::routes();
});
Route::get('/pdf-yukle', 'TreeController@pdfShow')->name('pdf.show');
Route::post('/pdf-yukle', 'TreeController@pdfStore')->name('pdf.store');
Route::get('/agac', 'TreeController@index')->name('tree.index');
Route::post('/agac', 'TreeController@store')->name('tree.store');
Route::delete('/agac/{slug}', 'TreeController@delete')->name('tree.delete');
Route::get('/{slug}', 'TreeController@show')->name('tree.show');

//Route::get('/home', 'HomeController@index')->name('home');
