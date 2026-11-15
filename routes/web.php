<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ActivityProgressController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeImportController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\EvidenceController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TpsTierController;
use App\Http\Controllers\WorkshopController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — TPS-G Portal
|--------------------------------------------------------------------------
| Login is required. Every route group below is limited to the role that
| owns the corresponding stage of the TPS Key Person lifecycle.
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    // 1. Dashboard & global search
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/search', [SearchController::class, 'index'])->name('search');

    // 2. Personal profile (Karyawan views their own dossier here)
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // 3. Activity: Karyawan/OMDD/Manager may read; Karyawan may create; Admin may edit/delete.
    //    HRD has no access to the activity log at all.
    Route::middleware('role:admin,karyawan,omdd,manager')->group(function () {
        Route::get('/activity', [ActivityController::class, 'index'])->name('activity');
        Route::get('/activity/create', [ActivityController::class, 'create'])->name('activity.create');
        Route::post('/activity', [ActivityController::class, 'store'])->name('activity.store');
        Route::get('/activity/{id}', [ActivityController::class, 'show'])->name('activity.show');
    });

    // Activity progress: the owner (Karyawan) submits updates; Admin ACCs + gives feedback.
    Route::post('/activity/{id}/progress', [ActivityProgressController::class, 'store'])
        ->middleware('role:karyawan,admin')->name('activity.progress.store');

    Route::patch('/activity/{id}/progress/{progressId}/review', [ActivityProgressController::class, 'review'])
        ->middleware('role:admin')->name('activity.progress.review');

    Route::post('/activity/{id}/followup', [ActivityController::class, 'storeFollowup'])
        ->middleware('role:admin')->name('activity.followup.store');

    Route::middleware('role:admin')->group(function () {
        Route::get('/activity/{id}/edit', [ActivityController::class, 'edit'])->name('activity.edit');
        Route::put('/activity/{id}', [ActivityController::class, 'update'])->name('activity.update');
        Route::delete('/activity/{id}', [ActivityController::class, 'destroy'])->name('activity.destroy');
    });

    // 4. Employee directory: Admin CRUD, other staff read-only.
    //    Static "create" route MUST be registered before the "{id}" wildcard.
    Route::middleware('role:admin')->group(function () {
        Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
    });

    // 4b. Bulk import from the HRD Excel workbook (Admin only).
    //     Registered before the "{id}" wildcard so "/employees/import" resolves here.
    Route::middleware('role:admin')->group(function () {
        Route::get('/employees/import', [EmployeeImportController::class, 'create'])->name('employees.import');
        Route::post('/employees/import', [EmployeeImportController::class, 'store'])->name('employees.import.store');
    });

    Route::middleware('role:admin,omdd,manager')->group(function () {
        Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/employees/{id}', [EmployeeController::class, 'show'])->name('employees.show');
    });

    Route::middleware('role:admin')->group(function () {
        Route::get('/employees/{id}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('/employees/{id}', [EmployeeController::class, 'update'])->name('employees.update');
        Route::delete('/employees/{id}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    });

    // 5. Workshop: everyone can browse; Karyawan can join; Admin manages.
    //    Static "create" route MUST be registered before the "{workshop}" wildcard.
    Route::middleware('role:admin')->group(function () {
        Route::get('/workshops/create', [WorkshopController::class, 'create'])->name('workshops.create');
        Route::post('/workshops', [WorkshopController::class, 'store'])->name('workshops.store');
    });

    // HRD may not browse the workshop catalogue; participants & staff keep access.
    Route::middleware('role:admin,karyawan,omdd,manager')->group(function () {
        Route::get('/workshops', [WorkshopController::class, 'index'])->name('workshops.index');
        Route::get('/workshops/{workshop}', [WorkshopController::class, 'show'])->name('workshops.show');
    });

    // Weekly attendance evidence: Karyawan submits, Admin/OMDD reviews.
    Route::post('/workshops/{workshop}/evidence', [EvidenceController::class, 'store'])
        ->middleware('role:karyawan')->name('workshops.evidence.store');

    Route::patch('/workshops/{workshop}/evidence/{evidence}/review', [EvidenceController::class, 'review'])
        ->middleware('role:admin,omdd')->name('workshops.evidence.review');

    Route::middleware('role:admin')->group(function () {
        Route::get('/workshops/{workshop}/edit', [WorkshopController::class, 'edit'])->name('workshops.edit');
        Route::put('/workshops/{workshop}', [WorkshopController::class, 'update'])->name('workshops.update');
        Route::delete('/workshops/{workshop}', [WorkshopController::class, 'destroy'])->name('workshops.destroy');
        Route::post('/workshops/{workshop}/participants', [WorkshopController::class, 'addParticipant'])->name('workshops.participants.store');
        Route::patch('/workshops/{workshop}/participants/{participant}/confirm', [WorkshopController::class, 'confirmParticipant'])->name('workshops.participants.confirm');
        Route::delete('/workshops/{workshop}/participants/{participant}', [WorkshopController::class, 'removeParticipant'])->name('workshops.participants.destroy');
    });

    // 6. Candidate pipeline: HRD recommends, Admin confirms, OMDD assesses.
    //    Static "create" route MUST be registered before the "{id}" wildcard.
    Route::middleware('role:hrd,admin')->group(function () {
        Route::get('/candidates/create', [CandidateController::class, 'create'])->name('candidates.create');
        Route::post('/candidates', [CandidateController::class, 'store'])->name('candidates.store');
    });

    Route::middleware('role:admin,hrd,omdd,manager')->group(function () {
        Route::get('/candidates', [CandidateController::class, 'index'])->name('candidates.index');
        Route::get('/candidates/{id}', [CandidateController::class, 'show'])->name('candidates.show');
    });

    Route::post('/candidates/{id}/assessment', [CandidateController::class, 'submitAssessment'])
        ->middleware('role:omdd')->name('candidates.assessment');
    Route::post('/candidates/{id}/approve', [CandidateController::class, 'approve'])
        ->middleware('role:admin')->name('candidates.approve');

    // 7. OMDD evaluation workspace.
    Route::middleware('role:omdd,admin')->group(function () {
        Route::get('/evaluations', [EvaluationController::class, 'index'])->name('evaluations.index');
    });

    Route::middleware('role:omdd')->group(function () {
        Route::get('/evaluations/{participant}', [EvaluationController::class, 'show'])->name('evaluations.show');
        Route::post('/evaluations/{participant}', [EvaluationController::class, 'store'])->name('evaluations.store');
    });

    // 8. Reports: Admin generates & sends, Manager and HRD receive/view.
    Route::middleware('role:admin,manager,hrd')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/activity', [ReportController::class, 'activity'])->name('reports.activity');
        Route::get('/reports/activity/export', [ReportController::class, 'exportActivity'])->name('reports.activity.export');
        Route::get('/reports/workshop/{workshop}', [ReportController::class, 'workshop'])->name('reports.workshop');
    });

    Route::middleware('role:admin')->group(function () {
        Route::post('/reports/workshop/{workshop}/generate', [ReportController::class, 'generate'])->name('reports.workshop.generate');
        Route::post('/reports/workshop/{workshop}/send', [ReportController::class, 'send'])->name('reports.workshop.send');
        Route::post('/reports/activity/send', [ReportController::class, 'sendActivity'])->name('reports.activity.send');
    });

    // 9. Internal messaging / report inbox.
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{message}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages', [MessageController::class, 'store'])
        ->middleware('role:admin,hrd,omdd')->name('messages.store');

    // 10. TPS tier directories & analytics (staff roles; HRD excluded).
    Route::middleware('role:admin,omdd,manager')->group(function () {
        Route::prefix('tps')->name('tps.')->group(function () {
            Route::get('/kp4', [TpsTierController::class, 'kp4'])->name('kp4');
            Route::get('/kp3', [TpsTierController::class, 'kp3'])->name('kp3');
            Route::get('/advance', [TpsTierController::class, 'advance'])->name('advance');
        });

        Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
        Route::get('/analytics/members', [AnalyticsController::class, 'members'])->name('analytics.members');
    });
});
