<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::get('admins', function () {
    return Inertia::render('admin/List');
})->middleware(['auth', 'verified'])->name('admin.list');

Route::get('companies', function () {
    return Inertia::render('companies/List');
})->middleware(['auth', 'verified'])->name('companies.list');


Route::get('employees', function () {
    return Inertia::render('employee/List');
})->middleware(['auth', 'verified'])->name('employees.list');

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
