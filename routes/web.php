<?php

use App\Http\Controllers\AlumniController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ImpersonationController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\LeaveRequestAttachmentController;
use App\Http\Controllers\MiscController;
use App\Http\Controllers\ParentPortalController;
use App\Http\Controllers\PaymentWebhookController;
use App\Http\Controllers\PPDBController;
use App\Http\Controllers\PPDBDocumentController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\StudentPortalController;
use App\Http\Controllers\WebsiteController;
use App\Http\Middleware\ResolveTenant;
use Illuminate\Support\Facades\Route;

// Public landing page (no auth, no tenant required)
Route::get('/', [LandingController::class, 'index'])->name('landing');

// All roles share one login page at /edusaas-admin/login.
// The super-admin and teacher panels have no login page of their own, so we
// register named redirect routes that Filament's auth middleware and logout
// response can resolve. The route names MUST match what Filament expects.
Route::get('/login', fn () => redirect('/edusaas-admin/login'))->name('login');
Route::get('/super-admin/login', fn () => redirect('/edusaas-admin/login'))->name('filament.super-admin.auth.login');
Route::get('/teacher/login', fn () => redirect('/edusaas-admin/login'))->name('filament.teacher.auth.login');

// Super admin "Enter Panel": open a school's admin panel and come back.
Route::middleware(['auth', 'user.type:super_admin'])->group(function () {
    Route::get('/super-admin/impersonate/{tenant}', [ImpersonationController::class, 'start'])->name('impersonate.start');
    Route::get('/impersonate/stop', [ImpersonationController::class, 'stop'])->name('impersonate.stop');
});

// Stores the chosen language in the session so the SetLocale middleware can
// apply it on every subsequent request. Invalid values are silently ignored.
Route::get('/locale/{locale}', [MiscController::class, 'switchLocale'])->name('locale.switch');

// School/tenant registration: public form for school owners to sign up,
// pick a plan, and pay for a subscription (or start a free trial).
// The payment page URL is signed so subscriptions cannot be enumerated.
Route::get('/register', [RegistrationController::class, 'create'])->name('register');
Route::post('/register', [RegistrationController::class, 'store'])->middleware('throttle:registration')->name('register.store');
Route::get('/register/payment/{subscription}', [RegistrationController::class, 'payment'])->middleware('signed')->name('register.payment');
Route::get('/register/success', [RegistrationController::class, 'success'])->name('register.success');
Route::get('/register/trial-success', [RegistrationController::class, 'trialSuccess'])->name('register.trial-success');

// Generic logout used by the student and parent portals.
Route::post('/logout', [MiscController::class, 'logout'])->name('logout');

// PPDB (student admission). Public visitors browse waves, register with
// documents, download their proof and check the result. Result pages use
// signed URLs; uploaded documents are only served to school staff.
Route::prefix('ppdb')->name('ppdb.')->middleware([ResolveTenant::class])->group(function () {
    Route::get('/', [PPDBController::class, 'index'])->name('index');
    Route::get('/register/{wave}', [PPDBController::class, 'register'])->name('register');
    Route::post('/register', [PPDBController::class, 'store'])->middleware('throttle:public-forms')->name('store');
    Route::get('/success/{registration}', [PPDBController::class, 'success'])->middleware('signed')->name('success');
    Route::get('/proof/{registration}', [PPDBController::class, 'proof'])->middleware('signed')->name('proof');
    Route::get('/status', [PPDBController::class, 'status'])->name('status');
    Route::post('/status', [PPDBController::class, 'checkStatus'])->middleware('throttle:public-forms')->name('check-status');
    Route::get('/acceptance/{registration}', [PPDBController::class, 'acceptanceLetter'])->middleware('signed')->name('acceptance-letter');
});

Route::get('/ppdb/documents/{registration}/{document}', [PPDBDocumentController::class, 'show'])
    ->middleware(['auth', 'tenant', 'tenant.required', 'user.type:school_admin,operator'])
    ->where('document', '[a-z_]+')
    ->name('ppdb.documents.show');

// QR-based attendance. Students must be signed in: the scan link sends guests
// to the login page and back, and attendance is recorded for that student.
Route::middleware(['auth', 'tenant', 'tenant.required', 'user.type:student', 'throttle:attendance'])->group(function () {
    Route::get('/attendance/scan', [AttendanceController::class, 'scan'])->name('attendance.scan');
    Route::post('/attendance/confirm', [AttendanceController::class, 'confirm'])->name('attendance.confirm');
});

// Public school website (profile, news, agenda, achievements, gallery, staff
// directory, contact), the alumni directory and the XML sitemap.
Route::middleware([ResolveTenant::class])->group(function () {
    Route::prefix('profile')->name('website.')->group(function () {
        Route::get('/', [WebsiteController::class, 'home'])->name('home');
        Route::get('/about', [WebsiteController::class, 'about'])->name('about');
        Route::get('/news', [WebsiteController::class, 'news'])->name('news');
        Route::get('/news/{slug}', [WebsiteController::class, 'newsShow'])->where('slug', '[A-Za-z0-9\-]+')->name('news.show');
        Route::get('/agenda', [WebsiteController::class, 'agenda'])->name('agenda');
        Route::get('/achievements', [WebsiteController::class, 'achievements'])->name('achievements');
        Route::get('/gallery', [WebsiteController::class, 'gallery'])->name('gallery');
        Route::get('/gallery/{slug}', [WebsiteController::class, 'album'])->where('slug', '[A-Za-z0-9\-]+')->name('gallery.show');
        Route::get('/teachers', [WebsiteController::class, 'teachers'])->name('teachers');
        Route::get('/contact', [WebsiteController::class, 'contact'])->name('contact');
    });

    Route::get('/sitemap.xml', [WebsiteController::class, 'sitemap'])->name('website.sitemap');
    Route::get('/alumni', [AlumniController::class, 'index'])->name('alumni.index');
});

// Payment gateway callbacks. These are POST endpoints called by Midtrans and
// Xendit servers so they must be exempted from CSRF verification (see
// bootstrap/app.php). Signature/token verification happens in the controller.
Route::middleware('throttle:webhooks')->group(function () {
    Route::post('/webhooks/midtrans', [PaymentWebhookController::class, 'midtrans'])->name('webhooks.midtrans');
    Route::post('/webhooks/midtrans/subscription', [PaymentWebhookController::class, 'midtransSubscription'])->name('webhooks.midtrans.subscription');
    Route::post('/webhooks/xendit', [PaymentWebhookController::class, 'xendit'])->name('webhooks.xendit');
});

// Routes both portals share (see PortalController). Registered inside each
// portal group so names become student.* and parent.*.
$sharedPortalRoutes = function (string $controller) {
    Route::get('/report-cards/{reportCard}/pdf', [$controller, 'reportCardPdf'])->name('report-cards.pdf');
    Route::post('/bills/{bill}/pay', [$controller, 'pay'])->middleware('throttle:public-forms')->name('bills.pay');
    Route::get('/payments/{payment}/receipt', [$controller, 'receipt'])->name('payments.receipt');
    Route::get('/announcements', [$controller, 'announcements'])->name('announcements');
    Route::get('/messages', [$controller, 'messages'])->name('messages');
    Route::post('/messages', [$controller, 'sendMessage'])->middleware('throttle:public-forms')->name('messages.send');
    Route::get('/messages/{thread}', [$controller, 'thread'])->where('thread', '[A-Za-z0-9\-]+')->name('messages.thread');
    Route::post('/messages/{thread}/reply', [$controller, 'reply'])->where('thread', '[A-Za-z0-9\-]+')->middleware('throttle:public-forms')->name('messages.reply');
    Route::post('/leave-requests', [$controller, 'storeLeaveRequest'])->middleware('throttle:public-forms')->name('leave-requests.store');
    Route::get('/profile', [$controller, 'profile'])->name('profile');
    Route::put('/profile/password', [$controller, 'updatePassword'])->middleware('throttle:public-forms')->name('profile.password');
};

// Student portal: the signed-in student's own academic, finance and school data.
Route::prefix('student-portal')->name('student.')->middleware(['auth', 'tenant', 'tenant.required', 'user.type:student'])->group(function () use ($sharedPortalRoutes) {
    Route::get('/', [StudentPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/schedule', [StudentPortalController::class, 'schedule'])->name('schedule');
    Route::get('/attendance', [StudentPortalController::class, 'attendance'])->name('attendance');
    Route::get('/grades', [StudentPortalController::class, 'grades'])->name('grades');
    Route::get('/report-cards', [StudentPortalController::class, 'reportCards'])->name('report-cards');
    Route::get('/bills', [StudentPortalController::class, 'bills'])->name('bills');
    Route::get('/activities', [StudentPortalController::class, 'activities'])->name('activities');
    Route::get('/leave-requests', [StudentPortalController::class, 'leaveRequests'])->name('leave-requests');
    Route::get('/savings', [StudentPortalController::class, 'savings'])->name('savings');
    Route::get('/discipline', [StudentPortalController::class, 'discipline'])->name('discipline');
    $sharedPortalRoutes(StudentPortalController::class);
});

// Parent portal: every child linked to the parent's email, one page set per child.
Route::prefix('parent-portal')->name('parent.')->middleware(['auth', 'tenant', 'tenant.required', 'user.type:parent'])->group(function () use ($sharedPortalRoutes) {
    Route::get('/', [ParentPortalController::class, 'dashboard'])->name('dashboard');
    Route::prefix('children/{student}')->group(function () {
        Route::get('/', [ParentPortalController::class, 'child'])->name('child');
        Route::get('/schedule', [ParentPortalController::class, 'schedule'])->name('schedule');
        Route::get('/attendance', [ParentPortalController::class, 'attendance'])->name('attendance');
        Route::get('/grades', [ParentPortalController::class, 'grades'])->name('grades');
        Route::get('/report-cards', [ParentPortalController::class, 'reportCards'])->name('report-cards');
        Route::get('/bills', [ParentPortalController::class, 'bills'])->name('bills');
        Route::get('/activities', [ParentPortalController::class, 'activities'])->name('activities');
        Route::get('/leave-requests', [ParentPortalController::class, 'leaveRequests'])->name('leave-requests');
        Route::get('/savings', [ParentPortalController::class, 'savings'])->name('savings');
        Route::get('/discipline', [ParentPortalController::class, 'discipline'])->name('discipline');
    });
    $sharedPortalRoutes(ParentPortalController::class);
});

// Leave request attachments live on the private disk; the controller allows
// school staff, the student's homeroom teacher and the requester only.
Route::get('/leave-requests/{leaveRequest}/attachment', [LeaveRequestAttachmentController::class, 'show'])
    ->middleware(['auth', 'tenant', 'tenant.required'])
    ->name('leave-requests.attachment');
