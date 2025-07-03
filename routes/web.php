<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\InvitationController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['guest'])->group(function () {
    Route::get('/', function () {
        return Inertia::render('auth/Login');
    })->name('home');

    Route::get('/password/{token}', function ($token) {
        if (!$token) {
            return Inertia::render('auth/Login');
        }
        return Inertia::render('auth/ChangePassword', ['token' => $token]);
    })->name('password');


    Route::get('/profile/update/{token}', function ($token) {
        if (!$token) {
            return Inertia::render('auth/Login');
        }
        return Inertia::render('employee/Profile', ['token' => $token]);
    });

    Route::post('/password/reset/{token}', [EmployeeController::class, 'passwordReset'])->name('password.reset.emp');
    Route::post('/update/profile/{token}', [EmployeeController::class, 'updateProfile'])->name('update.profile');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/companies', [CompanyController::class, 'index'])->name('companies.list');
    Route::get('/companies/create', [CompanyController::class, 'create'])->name('companies.create');
    Route::post('/companies/store', [CompanyController::class, 'store'])->name('companies.store');
    Route::get('/companies/{id}/edit', [CompanyController::class, 'edit'])->name('companies.edit');
    Route::post('/companies/update/{id}', [CompanyController::class, 'update'])->name('companies.update');
    Route::delete('/companies/delete/{company}', [CompanyController::class, 'destroy'])->name('companies.delete');

    Route::get('/admins', [AdminController::class, 'index'])->name('admins.list');
    Route::get('/admins/create', [AdminController::class, 'create'])->name('admins.create');
    Route::post('/admins/store', [AdminController::class, 'store'])->name('admins.store');
    Route::get('/admins/{user}/edit', [AdminController::class, 'show'])->name('admins.edit');
    Route::post('/admins/update/{user}', [AdminController::class, 'update'])->name('admins.update');
    Route::delete('/admins/delete/{user}', [AdminController::class, 'destroy'])->name('admins.delete');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/invitations', [InvitationController::class, 'index'])->name('invitations.list');
    Route::get('/invitations/create', [InvitationController::class, 'create'])->name('invitations.create');
    Route::post('/send/invitations', [InvitationController::class, 'sendInvitation'])->name('invitations.send');
    Route::get('/employees', [EmployeeController::class, 'empList'])->name('employees.list');
    Route::delete('/employee/{user}', [EmployeeController::class, 'destroy'])->name('employees.delete');
});
// employee routes
Route::get('/company/invitation/{token?}', [EmployeeController::class, 'index'])->name('invitation');
Route::get('/company/accept/invitation/{token?}', [EmployeeController::class, 'accept'])->name('accept.invitation');
Route::get('/company/reject/invitation/{token?}', [EmployeeController::class, 'reject'])->name('reject.invitation');

// Route::get('employees', function () {
//     return Inertia::render('employee/List');
// })->middleware(['auth', 'verified'])->name('employees.list');

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
