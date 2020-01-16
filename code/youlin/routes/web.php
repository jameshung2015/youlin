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


use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// 微信服务器接口


Route::match(['get','post'],'/official/account', 'WeChatController@officialAccount');
Route::get('/small/login', 'WeChatController@smallLogin');


Route::any('/test', 'WeChatController@log');

Route::get('/create', 'UserController@create');
//Route::get('/index', 'Controller@demo')->middleware('login');
