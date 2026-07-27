<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminSupportController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ScorecardController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ValueController;
use Illuminate\Support\Facades\Route;

// ── Public ────────────────────────────────────────────────────────────────────
Route::get('/', fn () => view('landing'))->name('home');
Route::get('/privacy-policy', fn () => view('privacy-policy'))->name('privacy-policy');

Route::get('/sitemap.xml', function () {
    $base = rtrim(config('app.url'), '/');
    $xml  = '<?xml version="1.0" encoding="UTF-8"?>'
          . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'
          . '<url><loc>' . $base . '/</loc>'
          . '<lastmod>' . now()->toAtomString() . '</lastmod>'
          . '<changefreq>weekly</changefreq><priority>1.0</priority>'
          . '</url>'
          . '</urlset>';
    return response($xml, 200, ['Content-Type' => 'application/xml']);
})->name('sitemap');

Route::get('/robots.txt', function () {
    $base = rtrim(config('app.url'), '/');
    $txt  = implode("\n", [
        'User-agent: *',
        'Allow: /',
        'Disallow: /admin/',
        'Disallow: /dashboard',
        'Disallow: /dashboard/',
        'Disallow: /login',
        'Disallow: /signup',
        'Disallow: /logout',
        'Disallow: /profile',
        'Disallow: /assessments/',
        'Disallow: /values/',
        'Disallow: /scorecard/',
        'Disallow: /settings/',
        'Disallow: /users/',
        'Disallow: /survey/',
        'Disallow: /support/',
        '',
        'Sitemap: ' . $base . '/sitemap.xml',
    ]);
    return response($txt, 200, ['Content-Type' => 'text/plain']);
})->name('robots');

// 10 contact submissions per IP per minute
Route::post('/contact', function (\Illuminate\Http\Request $request) {
    $data = $request->validate([
        'name'    => 'required|string|max:100',
        'org'     => 'nullable|string|max:150',
        'email'   => 'required|email|max:150',
        'service' => 'nullable|string|max:100',
        'message' => 'required|string|max:3000',
    ]);

    $body = implode("\n", [
        "Name: {$data['name']}",
        "Organisation: " . ($data['org'] ?? '—'),
        "Email: {$data['email']}",
        "Service Interest: " . ($data['service'] ?? '—'),
        "",
        "Message:",
        $data['message'],
    ]);

    \Illuminate\Support\Facades\Mail::raw($body, function ($msg) use ($data) {
        $msg->to('info@credibilityfactory.net')
            ->replyTo($data['email'], $data['name'])
            ->subject('CFA Enquiry from ' . $data['name']);
    });

    return response()->json(['ok' => true]);
})->middleware('throttle:10,1')->name('contact.send');

// ── Anonymous survey (no auth) ────────────────────────────────────────────────
Route::get('/survey/{token}',  [SurveyController::class, 'show'])->name('survey.show');
// 5 per IP per minute + 200 per assessment-token per hour (tenant fairness)
Route::post('/survey/{token}', [SurveyController::class, 'submit'])
    ->middleware(['throttle:5,1', 'throttle:survey-by-token'])
    ->name('survey.submit');

// ── Auth ──────────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.post');
    Route::get('/signup',  [AuthController::class, 'showSignup'])->name('signup');
    Route::post('/signup', [AuthController::class, 'signup'])
        ->middleware('throttle:5,1')
        ->name('signup.post');

    // Password reset (3 requests per IP per minute to prevent abuse)
    Route::get('/forgot-password',        [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password',       [AuthController::class, 'sendResetLink'])->middleware('throttle:3,1')->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password',        [AuthController::class, 'resetPassword'])->middleware('throttle:3,1')->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ── Authenticated (company admin + superadmin) ────────────────────────────────
Route::middleware(['auth', 'admin', 'throttle:tenant'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile',  [AuthController::class, 'profile'])->name('profile');
    Route::post('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Assessments
    Route::get('/assessments',          [AssessmentController::class, 'index'])->name('assessments.index');
    Route::post('/assessments',         [AssessmentController::class, 'store'])->name('assessments.store');
    Route::get('/assessments/export',   [AssessmentController::class, 'export'])->name('assessments.export');
    Route::post('/assessments/{assessment}/close',       [AssessmentController::class, 'close'])->name('assessments.close');
    Route::post('/assessments/{assessment}/recalculate', [AssessmentController::class, 'recalculate'])->name('assessments.recalculate');
    Route::delete('/assessments/{assessment}',           [AssessmentController::class, 'destroy'])->name('assessments.destroy');

    // Values
    Route::get('/setup/values',                  [ValueController::class, 'index'])->name('values.index');
    Route::post('/setup/values/bulk-save',        [ValueController::class, 'bulkSave'])->name('values.bulk-save');
    Route::delete('/setup/values/{value}',        [ValueController::class, 'destroy'])->name('values.destroy');

    // Scorecard & report
    Route::get('/scorecard/{assessment}',          [ScorecardController::class, 'show'])->name('scorecard.show');
    Route::get('/scorecard/{assessment}/download', [ReportController::class, 'download'])->name('report.download');

    // Support tickets (client)
    Route::get('/support',                               [SupportTicketController::class, 'index'])->name('support.index');
    Route::post('/support',                              [SupportTicketController::class, 'store'])->name('support.store');
    Route::get('/support/{ticket}',                      [SupportTicketController::class, 'show'])->name('support.show');
    Route::post('/support/{ticket}/messages',            [SupportTicketController::class, 'addMessage'])->name('support.messages');

    // Tenant activity log (own company events only)
    Route::get('/activity-log', [ActivityLogController::class, 'tenant'])->name('activity-log');
});

// ── SuperAdmin only ───────────────────────────────────────────────────────────
Route::middleware(['auth', 'superadmin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/',                     [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/settings',             [SettingsController::class, 'show'])->name('settings');
    Route::post('/settings',            [SettingsController::class, 'update'])->name('settings.update');

    // Admin activity log (all events, filterable by company)
    Route::get('/activity-log', [ActivityLogController::class, 'admin'])->name('activity-log');

    // Support tickets (admin)
    Route::get('/support',                            [AdminSupportController::class, 'index'])->name('support.index');
    Route::get('/support/{ticket}',                   [AdminSupportController::class, 'show'])->name('support.show');
    Route::post('/support/{ticket}/reply',            [AdminSupportController::class, 'reply'])->name('support.reply');
    Route::post('/support/{ticket}/resolve',          [AdminSupportController::class, 'resolve'])->name('support.resolve');
    Route::post('/support/{ticket}/reopen',           [AdminSupportController::class, 'reopen'])->name('support.reopen');

    // Companies
    Route::post('/companies/onboard',             [CompanyController::class, 'onboard'])->name('companies.onboard');
    Route::get('/companies',                      [CompanyController::class, 'index'])->name('companies.index');
    Route::get('/companies/{company}',            [CompanyController::class, 'show'])->name('companies.show');
    Route::put('/companies/{company}',            [CompanyController::class, 'update'])->name('companies.update');
    Route::delete('/companies/{company}',         [CompanyController::class, 'destroy'])->name('companies.destroy');
    Route::post('/companies/{company}/token',      [CompanyController::class, 'regenerateToken'])->name('companies.token');
    Route::post('/companies/{company}/grant-slot', [CompanyController::class, 'grantAssessmentSlot'])->name('companies.grant-slot');
    Route::post('/companies/{company}/users',      [CompanyController::class, 'storeUser'])->name('companies.users.store');

    // Users
    Route::get('/users',                       [UserController::class, 'index'])->name('users.index');
    Route::post('/users/superadmin',           [UserController::class, 'storeSuperAdmin'])->name('users.superadmin');
    Route::post('/users/{user}/toggle',        [UserController::class, 'toggleActive'])->name('users.toggle');
    Route::post('/users/{user}/password',      [UserController::class, 'changePassword'])->name('users.password');
    Route::delete('/users/{user}',             [UserController::class, 'destroy'])->name('users.destroy');
});
