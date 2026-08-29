<?php

use Illuminate\Support\Facades\Route;

// Ruta raíz (Carga el Login)
Route::get('/', function () {
    return view('auth.login');
});

// Rutas secundarias para los paneles
Route::get('/admin/dashboard', function () {
    return view('admin.dashboardAdmin');
});

Route::get('/company/dashboard', function () {
    return view('company.dashboardCompany');
});