<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderController;

Route::post('/order/normalize-txt', [OrderController::class, 'normalize']);
