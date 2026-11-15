<?php

use App\Http\Controllers\Api\ActivityPlanApiController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\EmployeeApiController;
use App\Http\Controllers\Api\MasterDataApiController;
use App\Http\Controllers\Api\MessageApiController;
use App\Http\Controllers\Api\WorkshopApiController;
use App\Http\Controllers\Api\WorkshopCandidateApiController;
use App\Http\Controllers\Api\WorkshopEvaluationApiController;
use App\Http\Controllers\Api\WorkshopHrdReviewApiController;
use App\Http\Controllers\Api\WorkshopParticipantApiController;
use App\Http\Controllers\Api\WorkshopScoreReportApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — TPS-G Portal (Employee & Workshop Management)
|--------------------------------------------------------------------------
| All API route names are prefixed with 'api.' to prevent collision with Web views.
*/

Route::name('api.')->group(function () {
    // 1. Dashboard Metrics & KPI
    Route::get('/dashboard/stats', [DashboardApiController::class, 'stats'])->name('dashboard.stats');

    // 2. Master Data (Plants, Divisions, Departments, Reference lists)
    Route::prefix('master')->name('master.')->group(function () {
        Route::get('/all', [MasterDataApiController::class, 'all'])->name('all');
        Route::get('/plants', [MasterDataApiController::class, 'plants'])->name('plants');
        Route::get('/divisions', [MasterDataApiController::class, 'divisions'])->name('divisions');
        Route::get('/departments', [MasterDataApiController::class, 'departments'])->name('departments');
    });

    // 3. Employee Management API
    Route::apiResource('employees', EmployeeApiController::class);

    // 4. Kaizen & Jishuken Activities API
    Route::apiResource('activities', ActivityPlanApiController::class);
    Route::post('activities/{id}/progress', [ActivityPlanApiController::class, 'addProgress'])->name('activities.progress');
    Route::post('activities/{id}/followup', [ActivityPlanApiController::class, 'addFollowup'])->name('activities.followup');

    // 5. Workshop Management API
    Route::apiResource('workshops', WorkshopApiController::class);

    // 6. Workshop 5-Stage Process API
    // Stage 1: TLC Recommendations
    Route::get('workshop-candidates', [WorkshopCandidateApiController::class, 'index'])->name('candidates.index');
    Route::post('workshop-candidates', [WorkshopCandidateApiController::class, 'store'])->name('candidates.store');
    Route::patch('workshop-candidates/{id}/review', [WorkshopCandidateApiController::class, 'review'])->name('candidates.review');

    // Stage 2: HRD Reviews
    Route::get('workshop-hrd-reviews', [WorkshopHrdReviewApiController::class, 'index'])->name('hrd-reviews.index');
    Route::post('workshop-hrd-reviews', [WorkshopHrdReviewApiController::class, 'store'])->name('hrd-reviews.store');

    // Stage 3: Admin Confirmation & Participants
    Route::get('workshop-participants', [WorkshopParticipantApiController::class, 'index'])->name('participants.index');
    Route::post('workshop-participants', [WorkshopParticipantApiController::class, 'store'])->name('participants.store');
    Route::patch('workshop-participants/{id}/status', [WorkshopParticipantApiController::class, 'updateStatus'])->name('participants.status');
    Route::delete('workshop-participants/{id}', [WorkshopParticipantApiController::class, 'destroy'])->name('participants.destroy');

    // Stage 4: OMDD Evaluations (ws_1 ... ws_7, final_decision)
    Route::apiResource('workshop-evaluations', WorkshopEvaluationApiController::class);

    // Stage 5: Score Reports & Distribution
    Route::get('workshop-score-reports', [WorkshopScoreReportApiController::class, 'index'])->name('score-reports.index');
    Route::post('workshop-score-reports', [WorkshopScoreReportApiController::class, 'store'])->name('score-reports.store');
    Route::post('workshop-score-reports/{id}/distribute', [WorkshopScoreReportApiController::class, 'distribute'])->name('score-reports.distribute');

    // 7. Internal Messaging & Distribution Chat
    Route::get('messages', [MessageApiController::class, 'index'])->name('messages.index');
    Route::post('messages', [MessageApiController::class, 'store'])->name('messages.store');
    Route::patch('messages/{id}/read', [MessageApiController::class, 'markAsRead'])->name('messages.read');
});
