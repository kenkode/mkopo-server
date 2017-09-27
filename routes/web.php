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

Route::get('/update_user', 'AndroidController@updateUser');

Route::get('/update_password', 'AndroidController@updatePassword');

Route::get("/authenticate_user", "AndroidController@authenticateUser");

Route::get('/apply_loan', 'AndroidController@applyLoan');

Route::get('/get_loans', 'AndroidController@getLoans');

Route::get('/get_balance', 'AndroidController@getBalance');

Route::get('/get_approved_loans', 'AndroidController@getApprovedLoans');

Route::get('/loan_details', 'AndroidController@loanDetails');

Route::get('/loan_status', 'AndroidController@loanStatus');

Route::get('/loan_history', 'AndroidController@loanHistory');

Route::get('/add_sms', 'AndroidController@smsTransactions');

Route::get('/kplc_sms', 'AndroidController@kplcTransactions');

Route::get('/nairobi_water_sms', 'AndroidController@nairobiWaterTransactions');

Route::get('/save_battery', 'AndroidController@checkBattery');

Route::get('/network_mode', 'AndroidController@networkMode');

Route::get('/bluetooth_status', 'AndroidController@bluetoothStatus');

Route::get('/save_mobile_info', 'AndroidController@saveMobileInfo');

Route::get('/call_sms', 'AndroidController@callSms');

Route::get('/add_location', 'AndroidController@addLocation');
