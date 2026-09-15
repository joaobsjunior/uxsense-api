<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Only a landing page is served over the "web" middleware group. The former
| "/info" route that exposed phpinfo() was removed: it disclosed the server
| configuration, loaded extensions, paths and environment to anyone.
|
*/

Route::get('/', function () {
    return view('welcome');
});
