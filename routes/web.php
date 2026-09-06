<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

// Ruta principal (Login)
Route::get('/', function () {
    return view('auth.login');
})->name('login');

// Ruta para Cerrar Sesión
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

// Redirección directa de /admin a /admin/dashboard
Route::redirect('/admin', '/admin/dashboard');

// Portal Cliente / Client Dashboard
Route::get('/client', function () {
    return view('client.dashboardClient');
})->name('client');

// Panel Administrador (Grupo de Rutas)
Route::prefix('admin')->name('admin.')->group(function () {
    
    Route::get('/dashboard', function () {
        return view('admin.dashboardAdmin');
    })->name('dashboard');

    Route::get('/requests', function () {
        return view('admin.requestsAdmin');
    })->name('requests');

    // Cambiado: Gestión de Lotes
    Route::get('/batches', function () {
        return view('admin.batchAdmin'); // o 'admin.batchesAdmin' según nombraste tu archivo
    })->name('batches');

    Route::get('/reports', function () {
        return view('admin.reportsAdmin');
    })->name('reports');

    // Cambiado: Usuarios (reemplaza a Clientes)
    Route::get('/users', function () {
        return view('admin.usersAdmin');
    })->name('users');

    Route::get('/configuration', function () {
        return view('admin.configurationAdmin');
    })->name('configuration');

    Route::get('/profile', function () {
        return view('admin.profileAdmin');
    })->name('profile');
    
    Route::get('/support', function () {
    return view('admin.supportAdmin');
    })->name('support');
});