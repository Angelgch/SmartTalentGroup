<?php

use Illuminate\Support\Facades\Route;

// Ruta principal (Login)
Route::get('/', function () {
    return view('auth.login');
})->name('login');

// Ruta Panel Administrador
Route::get('/admin', function () {
    return view('admin.dashboardAdmin');
})->name('admin');

// Ruta Portal Cliente / Company
Route::get('/company', function () {
    return view('company.dashboardCompany');
})->name('company');