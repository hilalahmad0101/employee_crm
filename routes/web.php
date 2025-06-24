<?php

use App\Http\Controllers\CompanyController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
Route::middleware(['auth', 'role:super_admin'])->get('/admin-only', function () {
    return 'Welcome Super Admin!';
});

Route::middleware(['auth', 'role:super_admin'])->group(function () {
    // Super Admin routes
});

Route::get('admins', function () {
    return Inertia::render('admin/List');
})->middleware(['auth', 'verified'])->name('admin.list');

Route::get('/companies', [CompanyController::class, 'index'])->name('companies.list');
Route::get('/companies/create', [CompanyController::class, 'create'])->name('companies.create');
Route::post('/companies/store', [CompanyController::class, 'store'])->name('companies.store');
Route::get('/companies/{id}/edit', [CompanyController::class, 'edit'])->name('companies.edit');
Route::post('/companies/update/{id}', [CompanyController::class, 'update'])->name('companies.update');
Route::delete('/companies/delete/{company}', [CompanyController::class, 'destroy'])->name('companies.delete');

Route::get('/check', function () {
    return 'Web route is working';
});



Route::get('employees', function () {
    return Inertia::render('employee/List');
})->middleware(['auth', 'verified'])->name('employees.list');

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
