<?php

use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], '/oauth', 'UserController@oauth');

Route::post('/logout', 'UserController@logout');

Route::post('/sms/send', 'SMSController@send');

Route::post('/sms/verify', 'SMSController@verify');
