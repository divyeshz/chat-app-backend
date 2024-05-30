<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ok('Welcome to Chat App API');
});
