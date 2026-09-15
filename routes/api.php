<?php

use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AttendanceSessionController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GradeController;
use App\Http\Controllers\Api\SppBillController;
use App\Http\Controllers\Api\StudentController;
use Illuminate\Support\Facades\Route;

// Mobile / external API. All authenticated routes require a Sanctum token
// and run inside the tenant middleware so queries are scoped to the token
// owner's school. Every route is rate limited (see AppServiceProvider).
Route::prefix('v1')->group(function () {
    // Public authentication endpoint. Returns a Sanctum personal access token.
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:api-login');

    Route::middleware(['auth:sanctum', 'tenant', 'tenant.required'])->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::apiResource('announcements', AnnouncementController::class)->only(['index', 'show']);

        // Student self-service.
        Route::middleware('user.type:student')->group(function () {
            Route::post('/attendance/scan', [AttendanceController::class, 'scan']);
            Route::get('/grades', [GradeController::class, 'index']);
            Route::get('/spp-bills', [SppBillController::class, 'index']);
        });

        // Staff: read students, manage attendance sessions.
        Route::middleware('user.type:school_admin,operator,teacher')->group(function () {
            Route::apiResource('students', StudentController::class)->only(['index', 'show']);
            Route::apiResource('attendance-sessions', AttendanceSessionController::class);
        });

        // School administration: change student records.
        Route::middleware('user.type:school_admin,operator')->group(function () {
            Route::apiResource('students', StudentController::class)->only(['store', 'update', 'destroy']);
        });
    });
});
