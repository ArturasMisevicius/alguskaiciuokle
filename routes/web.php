<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard redirect based on role
Route::get('/dashboard', function () {
    if (auth()->user()->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('user.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // User Management CRUD
    Route::get('/users', [AdminDashboardController::class, 'users'])->name('users');
    Route::get('/users/create', [AdminDashboardController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminDashboardController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminDashboardController::class, 'editUser'])->name('users.edit');
    Route::get('/users/{user}/calendar', [AdminDashboardController::class, 'userCalendar'])->name('users.calendar');
    Route::post('/users/{user}/calendar', [AdminDashboardController::class, 'saveUserCalendar'])->name('users.calendar.save');
    Route::patch('/users/{user}', [AdminDashboardController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminDashboardController::class, 'destroyUser'])->name('users.destroy');

    // Timesheet Management
    Route::get('/timesheets', [\App\Http\Controllers\Admin\TimesheetController::class, 'index'])->name('timesheets.index');
    Route::get('/timesheets/{timesheet}', [\App\Http\Controllers\Admin\TimesheetController::class, 'show'])->name('timesheets.show');
    Route::post('/timesheets/{timesheet}/approve', [\App\Http\Controllers\Admin\TimesheetController::class, 'approve'])->name('timesheets.approve');
    Route::post('/timesheets/{timesheet}/reject', [\App\Http\Controllers\Admin\TimesheetController::class, 'reject'])->name('timesheets.reject');
    Route::post('/timesheets/bulk-approve', [\App\Http\Controllers\Admin\TimesheetController::class, 'bulkApprove'])->name('timesheets.bulk-approve');

    // Project Management
    Route::resource('projects', \App\Http\Controllers\Admin\ProjectController::class);

    // Rate Card Management
    Route::resource('rate-cards', \App\Http\Controllers\Admin\RateCardController::class);
    Route::post('/rate-cards/{rateCard}/duplicate', [\App\Http\Controllers\Admin\RateCardController::class, 'duplicate'])->name('rate-cards.duplicate');

    // Tariff Management
    Route::resource('tariffs', \App\Http\Controllers\Admin\TariffController::class);

    // Company Management
    Route::resource('companies', \App\Http\Controllers\Admin\CompanyController::class);
});

// User Routes
Route::middleware(['auth', 'role:user'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [UserDashboardController::class, 'profile'])->name('profile');
    Route::patch('/profile', [UserDashboardController::class, 'updateProfile'])->name('profile.update');

    // Timesheet Management
    Route::get('/timesheets', [\App\Http\Controllers\User\TimesheetController::class, 'index'])->name('timesheets.index');
    Route::get('/timesheets/create', [\App\Http\Controllers\User\TimesheetController::class, 'create'])->name('timesheets.create');
    Route::post('/timesheets', [\App\Http\Controllers\User\TimesheetController::class, 'store'])->name('timesheets.store');
    Route::get('/timesheets/{timesheet}/edit', [\App\Http\Controllers\User\TimesheetController::class, 'edit'])->name('timesheets.edit');
    Route::patch('/timesheets/{timesheet}', [\App\Http\Controllers\User\TimesheetController::class, 'update'])->name('timesheets.update');
    Route::delete('/timesheets/{timesheet}', [\App\Http\Controllers\User\TimesheetController::class, 'destroy'])->name('timesheets.destroy');
    Route::post('/timesheets/{timesheet}/submit', [\App\Http\Controllers\User\TimesheetController::class, 'submit'])->name('timesheets.submit');
    Route::post('/timesheets/submit-week', [\App\Http\Controllers\User\TimesheetController::class, 'submitWeek'])->name('timesheets.submit-week');

    // Timer Management
    Route::post('/timesheets/timer/start', [\App\Http\Controllers\User\TimesheetController::class, 'startTimer'])->name('timesheets.timer.start');
    Route::post('/timesheets/{timesheet}/timer/stop', [\App\Http\Controllers\User\TimesheetController::class, 'stopTimer'])->name('timesheets.timer.stop');
});

// Original Breeze Profile Routes (available to all authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/profile-edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile-breeze', [ProfileController::class, 'update'])->name('profile.breeze.update');
    Route::delete('/profile-delete', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
