<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['message' => 'WebGIS Drainase API'];
});

Route::get('/up', function () {
    return ['status' => 'ok'];
});
