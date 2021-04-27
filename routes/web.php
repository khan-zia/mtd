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

Route::get('/', function () {
    return view('pages.home');
})->name('home');

Route::get('info', 'MtdController@message')->name('info');

Route::get('opps', 'MtdController@error')->name('error');

Route::get('auth-redirect', 'MtdController@handleRedirect');

Route::get('/get-tax-data', 'MtdController@getTaxData')->name('get-tax-data');

Route::get('/vat-return', 'MtdController@getVATReturn')->name('vat-return');

Route::post('/submit-return', 'MtdController@submitReturn')->name('submit-return');

Route::get('/submitReturn', 'MtdController@confirmReturn')->name('confirm-return');

Route::get('/update-return', function () {
    return view('pages.update-return');
})->name('update-return');