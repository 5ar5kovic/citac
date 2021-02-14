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
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::post('/home/unesi-stanje', 'HomeController@unesiStanje')->name('unesi-stanje');
Route::post('/home/unesi-stanje-bazdaren', 'HomeController@unesiStanjeBazdaren')->name('unesi-stanje-bazdaren');
Route::any('/pretraga', 'HomeController@pretraga')->name('pretraga');
Route::get('/korisnik', 'HomeController@korisnik')->name('korisnik');
Route::get('/izvestaj', 'HomeController@izvestaj')->name('izvestaj');
Route::any('/napravi-izvestaj', 'HomeController@napraviIzvestaj')->name('napravi-izvestaj');
Route::get('/izmena-podataka', 'HomeController@izmenaPodataka')->name('izmena-podataka');
Route::post('/upis-podataka', 'HomeController@upisPodataka')->name('upis-podataka');
Route::get('/ocekivani-prihod', 'HomeController@ocekivaniPrihod')->name('ocekivani-prihod');
Route::any('/ocekivani-prihod-rezultat', 'HomeController@ocekivaniPrihodRezultat')->name('ocekivani-prihod-rezultat');