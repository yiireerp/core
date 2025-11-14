<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

Route::get('/reset-password/{token}', function ($token) {
    return view('auth.reset-password', ['token' => $token]);
})->name('password.reset');

Route::get('/settings', function () {
    return view('settings');
})->name('settings');

Route::get('/change-password', function () {
    return view('settings');
})->name('password.change');

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');
