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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/add_user', 'AndroidController@addUser');

Route::get("/authenticate_user", "AndroidController@authenticateUser");

Route::get('/apply_loan', 'AndroidController@applyLoan');

Route::get('/get_loan', 'AndroidController@getLoans');

Route::get('/loan_status', 'AndroidController@loanStatus');

Route::get('/loan_history', 'AndroidController@loanHistory');

Route::get('/sms_transactions', 'AndroidController@smsTransactions');
