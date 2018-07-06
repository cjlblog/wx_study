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

Route::get('/', function () {
    return view('welcome');
});

Route::get('wx/index', 'WxController@index');
Route::get('wx/share', 'WxController@shareWx');
Route::get('wx/token', 'WxController@getWxAccessToken');
Route::get('wx/ticket', 'WxController@getTicket');

//验证是否来至微信的
Route::any('wx/validate', 'WxValidateController@wxValidate');

