<?php

use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], '/oauth', 'UserController@oauth');

Route::post('/registry', 'UserController@registry');

Route::post('/login', 'UserController@login');

Route::post('/thirtyLogin', 'UserController@thirtyLogin');

Route::post('/logout', 'UserController@logout');
