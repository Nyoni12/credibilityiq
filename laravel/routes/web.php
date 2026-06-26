<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ScorecardController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ValueController;
use Illuminate\Support\Facades\Route;

// ── Public ────────────────────────────────────────────────────────────────────
Route::get('/', fn () => view('landing'))->name('home');

// ── Anonymous survey (no auth) ────────────────────────────────────────────────
Route::get('/survey/{token}',  [SurveyController::class, 'show'])->name('survey.show');
Route::post('/survey/{token}', [SurveyController::class, 'submit'])->name('survey.submit');

// ── Auth ──────────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/signup',  [AuthController::class, 'showSignup'])->name('signup');
    Route::post('/signup', [AuthController::class, 'signup'])->name('signup.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ── Authenticated (company admin + superadmin) ────────────────────────────────
Route::middleware(['auth', 'admin'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile',  [AuthController::class, 'profile'])->name('profile');
    Route::post('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Assessments
    Route::get('/assessments',          [AssessmentController::class, 'index'])->name('assessments.index');
    Route::post('/assessments',         [AssessmentController::class, 'store'])->name('assessments.store');
    Route::post('/assessments/{assessment}/close',  [AssessmentController::class, 'close'])->name('assessments.close');
    Route::delete('/assessments/{assessment}',      [AssessmentController::class, 'destroy'])->name('assessments.destroy');

    // Values
    Route::get('/setup/values',               [ValueController::class, 'index'])->name('values.index');
    Route::post('/setup/values',              [ValueController::class, 'store'])->name('values.store');
    Route::put('/setup/values/{value}',       [ValueController::class, 'update'])->name('values.update');
    Route::delete('/setup/values/{value}',    [ValueController::class, 'destroy'])->name('values.destroy');
    Route::post('/setup/values/reorder',      [ValueController::class, 'reorder'])->name('values.reorder');

    // Scorecard & report
    Route::get('/scorecard/{assessment}',          [ScorecardController::class, 'show'])->name('scorecard.show');
    Route::get('/scorecard/{assessment}/download', [ReportController::class, 'download'])->name('report.download');
});

// ── SuperAdmin only ───────────────────────────────────────────────────────────
Route::middleware(['auth', 'superadmin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/',                     [AdminController::class, 'dashboard'])->name('dashboard');

    // Companies
    Route::get('/companies',                    [CompanyController::class, 'index'])->name('companies.index');
    Route::get('/companies/{company}',          [CompanyController::class, 'show'])->name('companies.show');
    Route::put('/companies/{company}',          [CompanyController::class, 'update'])->name('companies.update');
    Route::delete('/companies/{company}',       [CompanyController::class, 'destroy'])->name('companies.destroy');
    Route::post('/companies/{company}/token',   [CompanyController::class, 'regenerateToken'])->name('companies.token');
    Route::post('/companies/{company}/users',   [CompanyController::class, 'storeUser'])->name('companies.users.store');

    // Users
    Route::get('/users',                    [UserController::class, 'index'])->name('users.index');
    Route::post('/users/{user}/toggle',     [UserController::class, 'toggleActive'])->name('users.toggle');
    Route::delete('/users/{user}',          [UserController::class, 'destroy'])->name('users.destroy');
});
