<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ParentPortalController;
use App\Http\Controllers\PaymentWebhookController;
use App\Http\Controllers\PPDBController;
use App\Http\Controllers\StudentPortalController;
use App\Http\Middleware\ResolveTenant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Landing page
Route::get('/', [LandingController::class, 'index'])->name('landing');

// Locale switcher
Route::get('/locale/{locale}', function (string $locale) {
    if (in_array($locale, ['id', 'en'])) {
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('locale.switch');

// Logout (for portals)
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// PPDB (public, no auth, but needs tenant)
Route::prefix('ppdb')->name('ppdb.')->middleware([ResolveTenant::class])->group(function () {
    Route::get('/', [PPDBController::class, 'index'])->name('index');
    Route::get('/register/{wave}', [PPDBController::class, 'register'])->name('register');
    Route::post('/register', [PPDBController::class, 'store'])->name('store');
    Route::get('/status', [PPDBController::class, 'status'])->name('status');
    Route::post('/status', [PPDBController::class, 'checkStatus'])->name('check-status');
    Route::get('/acceptance/{id}', [PPDBController::class, 'acceptanceLetter'])->name('acceptance-letter');
});

// Attendance scan (public, mobile)
Route::get('/attendance/scan', [AttendanceController::class, 'scan'])->name('attendance.scan');
Route::post('/attendance/confirm', [AttendanceController::class, 'confirm'])->name('attendance.confirm');

// Payment webhooks (no CSRF)
Route::post('/webhooks/midtrans', [PaymentWebhookController::class, 'midtrans'])->name('webhooks.midtrans');
Route::post('/webhooks/xendit', [PaymentWebhookController::class, 'xendit'])->name('webhooks.xendit');

// Student Portal (auth required)
Route::prefix('student-portal')->name('student.')->middleware(['auth', 'tenant', 'tenant.required'])->group(function () {
    Route::get('/', [StudentPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/attendance', [StudentPortalController::class, 'attendance'])->name('attendance');
    Route::get('/grades', [StudentPortalController::class, 'grades'])->name('grades');
    Route::get('/rapor/{semester}', [StudentPortalController::class, 'rapor'])->name('rapor');
    Route::get('/spp', [StudentPortalController::class, 'spp'])->name('spp');
    Route::post('/spp/{bill}/pay', [StudentPortalController::class, 'pay'])->name('pay');
    Route::get('/announcements', [StudentPortalController::class, 'announcements'])->name('announcements');
});

// Parent Portal (auth required)
Route::prefix('parent-portal')->name('parent.')->middleware(['auth', 'tenant', 'tenant.required'])->group(function () {
    Route::get('/', [ParentPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/attendance/{student}', [ParentPortalController::class, 'attendance'])->name('attendance');
    Route::get('/grades/{student}', [ParentPortalController::class, 'grades'])->name('grades');
    Route::get('/rapor/{student}/{semester}', [ParentPortalController::class, 'rapor'])->name('rapor');
    Route::get('/spp/{student}', [ParentPortalController::class, 'spp'])->name('spp');
    Route::post('/spp/{bill}/pay', [ParentPortalController::class, 'pay'])->name('pay');
    Route::get('/messages', [ParentPortalController::class, 'messages'])->name('messages');
});
