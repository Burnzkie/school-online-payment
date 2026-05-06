<?php
// routes/web.php

use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Student\BillingController;
use App\Http\Controllers\Student\StatementController;
use App\Http\Controllers\Student\StudentProfileController;

// ── HS Controllers ──────────────────────────────────────────────────────────
use App\Http\Controllers\Student\HS\HSDashboardController;
use App\Http\Controllers\Student\HS\HSBillingController;
use App\Http\Controllers\Student\HS\HSStatementController;
use App\Http\Controllers\Student\HS\HSProfileController;

// ── Cashier Controllers ─────────────────────────────────────────────────────
use App\Http\Controllers\Cashier\CashierController;

// ── Treasurer Controllers ───────────────────────────────────────────────────
use App\Http\Controllers\Treasurer\TreasurerController;

// ── Admin Controllers ───────────────────────────────────────────────────────
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\InvitationCodeController;

use Illuminate\Support\Facades\Route;

// _____Parent Controller_______
use App\Http\Controllers\Parent\ParentController;

use App\Http\Controllers\UserSettingsController;

/*
|--------------------------------------------------------------------------
| Public API — Student Search (used on the registration form)
| No auth required: parent searches for their child before they have an account.
| Only safe fields are returned (name, year level, student ID) — no billing data.
|--------------------------------------------------------------------------
*/
Route::get('/api/students/search', [ParentController::class, 'searchStudents'])
     ->name('api.students.search');

/*
|--------------------------------------------------------------------------
| Dark Mode Toggle — Shared Across ALL Portals
| Works for every role: admin, cashier, treasurer, parent, student (college + hs)
| Auto-saves to the users table (dark_mode column) via AJAX on toggle.
|--------------------------------------------------------------------------
*/
Route::middleware('auth')
     ->post('/user/settings/dark-mode', [UserSettingsController::class, 'toggleDarkMode'])
     ->name('user.settings.dark-mode');

/*
|--------------------------------------------------------------------------
| Parent Portal Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', \App\Http\Middleware\EnsureParent::class])
     ->prefix('parent')
     ->name('parent.')
     ->group(function () {

    // Dashboard
    Route::get('/',           [ParentController::class, 'dashboard'])->name('dashboard');

    // Per-student views
    Route::get('/student/{student}',                  [ParentController::class, 'studentDetail'])->name('student.detail');
    Route::get('/student/{student}/payments',         [ParentController::class, 'paymentHistory'])->name('student.payments');
    Route::get('/student/{student}/statement',        [ParentController::class, 'statement'])->name('student.statement');

    // Notifications
    Route::get('/notifications',                      [ParentController::class, 'notifications'])->name('notifications');
    Route::patch('/notifications/{id}/read',          [ParentController::class, 'markNotificationRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read',       [ParentController::class, 'markAllRead'])->name('notifications.readAll');

    // Profile
    Route::get('/profile',                            [ParentController::class, 'profile'])->name('profile');
    Route::get('/profile/edit',                       [ParentController::class, 'editProfile'])->name('profile.edit');
    Route::patch('/profile',                          [ParentController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/update-photo',              [ParentController::class, 'updateProfilePhoto'])->name('profile.update-photo');
});

/*
|--------------------------------------------------------------------------
| Home / Dashboard Redirect
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return auth()->check()
        ? redirect('/dashboard')
        : redirect('/register');
});

/**
 * Central dashboard redirect — routes each role/level to the right portal.
 */
Route::get('/dashboard', function () {
    $user = auth()->user();

    if (!$user) {
        return redirect('/register');
    }

    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    if ($user->role === 'cashier') {
        return redirect()->route('cashier.dashboard');
    }

    if ($user->role === 'treasurer') {
        return redirect()->route('treasurer.dashboard');
    }

    if ($user->role === 'parent') {
        return redirect()->route('parent.dashboard');
    }

    if ($user->role === 'student') {
        $levelGroup = strtolower($user->level_group ?? '');

        if (str_contains($levelGroup, 'junior') || str_contains($levelGroup, 'senior')) {
            return redirect()->route('hs.dashboard');
        }

        return redirect()->route('student.dashboard');
    }

    // Fallback for any unrecognized role
    return redirect('/register')->with('status', 'Your account role (' . $user->role . ') does not have a portal yet. Please contact the administrator.');
})->middleware(['auth'])->name('dashboard');

require __DIR__ . '/auth.php';
// Email verification routes (verification.notice / verification.verify / verification.send)
// are defined inside auth.php.

/*
|--------------------------------------------------------------------------
| Admin Portal Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', \App\Http\Middleware\EnsureAdmin::class])
     ->prefix('admin')
     ->name('admin.')
     ->group(function () {

    // Dashboard
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    // ── Users & Roles ──────────────────────────────────────────────────────
    Route::get('/users',                             [AdminController::class, 'users'])->name('users');
    Route::get('/users/create',                      [AdminController::class, 'userCreate'])->name('users.create');
    Route::post('/users',                            [AdminController::class, 'userStore'])->name('users.store');
    Route::get('/users/{user}/edit',                 [AdminController::class, 'userEdit'])->name('users.edit');
    Route::patch('/users/{user}',                    [AdminController::class, 'userUpdate'])->name('users.update');
    Route::patch('/users/{user}/reset-password',     [AdminController::class, 'userResetPassword'])->name('users.reset-password');
    Route::delete('/users/{user}',                   [AdminController::class, 'userDestroy'])->name('users.destroy');

    // ── Students ───────────────────────────────────────────────────────────
    Route::get('/students',                          [AdminController::class, 'students'])->name('students');
    Route::get('/students/{student}',                [AdminController::class, 'studentDetail'])->name('students.detail');
    Route::post('/students/{student}/link-parent',   [AdminController::class, 'studentLinkParent'])->name('students.link-parent');
    Route::patch('/students/{student}/drop',         [AdminController::class, 'studentDrop'])->name('students.drop');
    Route::patch('/students/{student}/reinstate',    [AdminController::class, 'studentReinstate'])->name('students.reinstate');

    // ── Fee Management ─────────────────────────────────────────────────────
    Route::get('/fees',                              [AdminController::class, 'fees'])->name('fees');
    Route::get('/fees/create',                       [AdminController::class, 'feeCreate'])->name('fees.create');
    Route::post('/fees',                             [AdminController::class, 'feeStore'])->name('fees.store');
    Route::get('/fees/bulk-create',                  [AdminController::class, 'feeBulkCreate'])->name('fees.bulk-create');
    Route::post('/fees/bulk',                        [AdminController::class, 'feeBulkStore'])->name('fees.bulk-store');
    Route::get('/fees/{fee}/edit',                   [AdminController::class, 'feeEdit'])->name('fees.edit');
    Route::patch('/fees/{fee}',                      [AdminController::class, 'feeUpdate'])->name('fees.update');
    Route::delete('/fees/{fee}',                     [AdminController::class, 'feeDestroy'])->name('fees.destroy');

    // ── Payments ───────────────────────────────────────────────────────────
    Route::get('/payments',                          [AdminController::class, 'payments'])->name('payments');
    Route::get('/payments/{payment}',                [AdminController::class, 'paymentShow'])->name('payments.show');
    Route::patch('/payments/{payment}/void',         [AdminController::class, 'paymentVoid'])->name('payments.void');

    // ── Scholarships ───────────────────────────────────────────────────────
    Route::get('/scholarships',                      [AdminController::class, 'scholarships'])->name('scholarships');
    Route::get('/scholarships/create',               [AdminController::class, 'scholarshipCreate'])->name('scholarships.create');
    Route::post('/scholarships',                     [AdminController::class, 'scholarshipStore'])->name('scholarships.store');
    Route::patch('/scholarships/{scholarship}/revoke',[AdminController::class, 'scholarshipRevoke'])->name('scholarships.revoke');

    // ── Clearances ─────────────────────────────────────────────────────────
    Route::get('/clearances',                        [AdminController::class, 'clearances'])->name('clearances');
    Route::patch('/clearances/{clearance}/grant',    [AdminController::class, 'clearanceGrant'])->name('clearances.grant');
    Route::patch('/clearances/{clearance}/hold',     [AdminController::class, 'clearanceHold'])->name('clearances.hold');


    // ── Reports ────────────────────────────────────────────────────────────
    Route::get('/reports',                           [AdminController::class, 'reports'])->name('reports');

    // ── Settings ───────────────────────────────────────────────────────────
    Route::get('/settings',                          [AdminController::class, 'settings'])->name('settings');
    Route::patch('/settings',                        [AdminController::class, 'settingsUpdate'])->name('settings.update');

    // ── Profile ────────────────────────────────────────────────────────────
    Route::get('/profile',                           [AdminController::class, 'profile'])->name('profile');
    Route::patch('/profile',                         [AdminController::class, 'profileUpdate'])->name('profile.update');
    Route::post('/profile/update-photo',             [AdminController::class, 'profileUpdatePhoto'])->name('profile.update-photo');
    Route::patch('/profile/change-password',         [AdminController::class, 'profileChangePassword'])->name('profile.change-password');

    // ── Invitation Codes ───────────────────────────────────────────────────
    Route::get('/invitation-codes',                        [InvitationCodeController::class, 'index'])->name('invitation-codes');
    Route::post('/invitation-codes',                       [InvitationCodeController::class, 'store'])->name('invitation-codes.store');
    Route::delete('/invitation-codes/{invitationCode}',    [InvitationCodeController::class, 'destroy'])->name('invitation-codes.destroy');
});

/*
|--------------------------------------------------------------------------
| College Student Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', \App\Http\Middleware\EnsureStudentIsActive::class])->prefix('student')->name('student.')->group(function () {

    Route::get('/dashboard', [StudentDashboardController::class, 'index'])
         ->name('dashboard');

    Route::get('/payment/create', [StudentDashboardController::class, 'paymentCreate'])
         ->name('payment.create');

    Route::get('/billing', [BillingController::class, 'index'])
         ->name('billing');

    Route::post('/billing/pay-online', [BillingController::class, 'payOnline'])
         ->name('billing.pay-online');


    Route::get('/statements', [StatementController::class, 'index'])
         ->name('statements');

    Route::get('/profile', [StudentProfileController::class, 'index'])
         ->name('profile');

    Route::post('/profile/update-photo', [StudentProfileController::class, 'updateProfilePhoto'])
         ->name('profile.update-photo');

    Route::get('/profile/edit', [StudentProfileController::class, 'edit'])
         ->name('profile.edit');

    Route::patch('/profile', [StudentProfileController::class, 'update'])
         ->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| Junior / Senior High School Student Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', \App\Http\Middleware\EnsureHighSchoolStudent::class, \App\Http\Middleware\EnsureStudentIsActive::class])
     ->prefix('hs')
     ->name('hs.')
     ->group(function () {

    Route::get('/dashboard', [HSDashboardController::class, 'index'])
         ->name('dashboard');

    Route::get('/billing', [HSBillingController::class, 'index'])
         ->name('billing');

    Route::post('/billing/pay-online', [HSBillingController::class, 'payOnline'])
         ->name('billing.pay-online');



    Route::get('/statements', [HSStatementController::class, 'index'])
         ->name('statements');

    Route::get('/profile', [HSProfileController::class, 'index'])
         ->name('profile');

    Route::post('/profile/update-photo', [HSProfileController::class, 'updateProfilePhoto'])
         ->name('profile.update-photo');

    Route::get('/profile/edit', [HSProfileController::class, 'edit'])
         ->name('profile.edit');

    Route::patch('/profile', [HSProfileController::class, 'update'])
         ->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| Cashier Portal Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', \App\Http\Middleware\EnsureCashier::class])
     ->prefix('cashier')
     ->name('cashier.')
     ->group(function () {

    // Dashboard
    Route::get('/', [CashierController::class, 'dashboard'])
         ->name('dashboard');

    // Student management
    Route::get('/students', [CashierController::class, 'students'])
         ->name('students');

    Route::get('/students/search', [CashierController::class, 'searchStudents'])
         ->name('students.search');

    Route::get('/students/{student}/ledger', [CashierController::class, 'studentLedger'])
         ->name('student-ledger');

    // Receive payment
    Route::get('/receive-payment', [CashierController::class, 'receivePaymentForm'])
         ->name('receive-payment');

    Route::post('/receive-payment', [CashierController::class, 'storePayment'])
         ->name('receive-payment.store');

    // Online payment submissions (from student portal)
    Route::get('/online-payments',                                    [CashierController::class, 'onlinePayments'])
         ->name('online-payments');

    Route::get('/online-payments/{submission}',                       [CashierController::class, 'onlinePaymentShow'])
         ->name('online-payments.show');

    Route::patch('/online-payments/{submission}/verify',              [CashierController::class, 'onlinePaymentVerify'])
         ->name('online-payments.verify');

    Route::patch('/online-payments/{submission}/reject',              [CashierController::class, 'onlinePaymentReject'])
         ->name('online-payments.reject');

    // Transactions & receipts
    Route::get('/transactions', [CashierController::class, 'transactions'])
         ->name('transactions');

    Route::get('/receipt/{payment}', [CashierController::class, 'receipt'])
         ->name('receipt');

    Route::patch('/payments/{payment}/complete', [CashierController::class, 'completePayment'])
         ->name('complete-payment');

    // Profile
    Route::get('/profile',                [CashierController::class, 'profile'])
         ->name('profile');

    Route::patch('/profile',              [CashierController::class, 'updateProfile'])
         ->name('profile.update');

    Route::patch('/profile/password',     [CashierController::class, 'updatePassword'])
         ->name('profile.update-password');

    Route::post('/profile/photo',         [CashierController::class, 'updatePhoto'])
         ->name('profile.update-photo');
});

/*
|--------------------------------------------------------------------------
| Treasurer Portal Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', \App\Http\Middleware\EnsureTreasurer::class])
     ->prefix('treasurer')
     ->name('treasurer.')
     ->group(function () {

    // Dashboard
    Route::get('/', [TreasurerController::class, 'dashboard'])->name('dashboard');

    // Fee Management
    Route::get('/fees',                      [TreasurerController::class, 'fees'])->name('fees');
    Route::get('/fees/create',               [TreasurerController::class, 'feesCreate'])->name('fees.create');
    Route::get('/fees/bulk-create',          [TreasurerController::class, 'feesBulkCreate'])->name('fees.bulk-create');
    Route::get('/fees/batch-count',          [TreasurerController::class, 'feesBatchCount'])->name('fees.batch-count');
    Route::post('/fees',                     [TreasurerController::class, 'feesStore'])->name('fees.store');
    Route::post('/fees/bulk',                [TreasurerController::class, 'feesBulkStore'])->name('fees.bulk-store');
    Route::get('/fees/{fee}/edit',           [TreasurerController::class, 'feesEdit'])->name('fees.edit');
    Route::patch('/fees/{fee}',              [TreasurerController::class, 'feesUpdate'])->name('fees.update');
    Route::delete('/fees/{fee}',             [TreasurerController::class, 'feesDestroy'])->name('fees.destroy');

    // Payments (read-only view)
    Route::get('/payments',             [TreasurerController::class, 'payments'])->name('payments');
    Route::get('/payments/{payment}',   [TreasurerController::class, 'paymentShow'])->name('payments.show');

    // Students Overview
    Route::get('/students',                                    [TreasurerController::class, 'students'])->name('students');
    Route::get('/students/{student}',                          [TreasurerController::class, 'studentDetail'])->name('student.detail');

    // Statement of Account (SOA)
    Route::get('/students/{student}/soa',                      [TreasurerController::class, 'soa'])->name('soa');


    // Aging Report
    Route::get('/aging',                                       [TreasurerController::class, 'aging'])->name('aging');

    // Scholarships & Discounts
    Route::get('/scholarships',                                [TreasurerController::class, 'scholarships'])->name('scholarships');
    Route::get('/scholarships/create',                         [TreasurerController::class, 'scholarshipCreate'])->name('scholarships.create');
    Route::post('/scholarships',                               [TreasurerController::class, 'scholarshipStore'])->name('scholarships.store');
    Route::patch('/scholarships/{scholarship}/revoke',         [TreasurerController::class, 'scholarshipRevoke'])->name('scholarships.revoke');

    // Clearance / Hold Management
    Route::get('/clearances',                                  [TreasurerController::class, 'clearances'])->name('clearances');
    Route::patch('/clearances/{clearance}/grant',              [TreasurerController::class, 'clearanceGrant'])->name('clearances.grant');
    Route::patch('/clearances/{clearance}/hold',               [TreasurerController::class, 'clearanceHold'])->name('clearances.hold');

    // Reports
    Route::get('/reports',                                     [TreasurerController::class, 'reports'])->name('reports');

    // Profile
    Route::get('/profile',                                     [TreasurerController::class, 'profile'])->name('profile');
    Route::get('/profile/edit',                                [TreasurerController::class, 'editProfile'])->name('profile.edit');
    Route::patch('/profile',                                   [TreasurerController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/update-photo',                       [TreasurerController::class, 'updateProfilePhoto'])->name('profile.update-photo');
});