<?php

use Illuminate\Support\Facades\Route;

// Mobile / external API. All authenticated routes require a Sanctum token
// and run inside the tenant middleware so queries are scoped automatically.
Route::prefix('v1')->group(function () {
    // Public authentication endpoint. Returns a Sanctum personal access token.
    Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

    Route::middleware(['auth:sanctum', 'tenant', 'tenant.required'])->group(function () {
        Route::get('/me', [\App\Http\Controllers\Api\AuthController::class, 'me']);
        Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);

        Route::apiResource('students', \App\Http\Controllers\Api\StudentController::class);
        Route::apiResource('attendance-sessions', \App\Http\Controllers\Api\AttendanceSessionController::class);
        Route::post('/attendance/scan', [\App\Http\Controllers\Api\AttendanceController::class, 'scan']);
        Route::get('/grades', [\App\Http\Controllers\Api\GradeController::class, 'index']);
        Route::get('/spp-bills', [\App\Http\Controllers\Api\SppBillController::class, 'index']);
        Route::apiResource('announcements', \App\Http\Controllers\Api\AnnouncementController::class)->only(['index', 'show']);
    });
});
