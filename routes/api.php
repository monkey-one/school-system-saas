<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // Auth
    Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

    Route::middleware(['auth:sanctum', 'tenant', 'tenant.required'])->group(function () {
        Route::get('/me', [\App\Http\Controllers\Api\AuthController::class, 'me']);
        Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);

        // Students
        Route::apiResource('students', \App\Http\Controllers\Api\StudentController::class);

        // Attendance
        Route::apiResource('attendance-sessions', \App\Http\Controllers\Api\AttendanceSessionController::class);
        Route::post('/attendance/scan', [\App\Http\Controllers\Api\AttendanceController::class, 'scan']);

        // Grades
        Route::get('/grades', [\App\Http\Controllers\Api\GradeController::class, 'index']);

        // SPP Bills
        Route::get('/spp-bills', [\App\Http\Controllers\Api\SppBillController::class, 'index']);

        // Announcements
        Route::apiResource('announcements', \App\Http\Controllers\Api\AnnouncementController::class)->only(['index', 'show']);
    });
});
