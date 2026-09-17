<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->file(base_path('index.html'));
});
