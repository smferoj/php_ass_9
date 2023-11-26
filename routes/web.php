<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class,'homePage']);
Route::get('/about', [HomeController::class,'aboutPage']);
Route::get('/projects', [HomeController::class,'projectsPage']);
Route::get('/contact', [HomeController::class,'contactPage']);





