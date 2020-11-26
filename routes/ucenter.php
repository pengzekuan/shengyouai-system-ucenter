<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    echo 'ucenter';
});

Route::post('/registry', 'UserController@registry');

Route::post('/login', 'UserController@login');

Route::post('/thirtyLogin', 'UserController@thirtyLogin');

Route::post('/logout', 'UserController@logout');
