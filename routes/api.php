<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\AcceptInvitationController;
use App\Http\Controllers\RegisterEmployeeController;

// ✅ Test route to verify API is working
// Route::get('/test-api-route', function () {
//     return response()->json(['message' => 'API routes file is working']);
// });

// // ✅ Public or basic authenticated routes (e.g., employees accepting invites)
// Route::middleware(['auth:sanctum'])->group(function () {
//     Route::get('/accept-invitation', [AcceptInvitationController::class, 'show']);
//     Route::post('/register-employee', [RegisterEmployeeController::class, 'store']);
// });

// // ✅ Super admin routes
// Route::middleware(['auth:sanctum', 'role:super_admin'])->group(function () {
//     Route::apiResource('companies', CompanyController::class);

//     Route::post('/invitations', [InvitationController::class, 'store']);
//     Route::delete('/invitations/{id}', [InvitationController::class, 'cancel']);
// });
