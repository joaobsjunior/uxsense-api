<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AnalyzeController;
use App\Http\Controllers\AnswerController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PushNotificationController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\SchedulerController;
use App\Http\Controllers\SubgroupController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TechniqueController;
use App\Http\Controllers\UnitController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Every route below is prefixed with "/api" and runs in the "api" middleware
| group (rate limited, stateless). Authentication:
|
|   auth.admin  - manager web app  (GSX-CODE + GSX-TOKEN headers)
|   auth.client - mobile app       (GSX-DEVICE + GSX-TOKEN headers)
|   auth.any    - either of the above
|   auth.cron   - scheduled jobs   (GSX-CRON-TOKEN header, see PUSH_CHECK_TOKEN)
|
*/

Route::get('', function () {
    return response()->json(["API SERVER"], 200);
});

// Triggered by the cron job that dispatches pending push notifications.
Route::get('push/check', [PushNotificationController::class, 'checkPush'])->middleware('auth.cron');

// Capture techniques are read by both the manager and the mobile app.
Route::get('manager/technique', [TechniqueController::class, 'index'])->middleware('auth.any');

Route::group(['prefix' => 'manager'], function () {

    Route::group(['middleware' => ['throttle:auth']], function () {
        Route::post('login', [LoginController::class, 'postIndex']);
        Route::post('login/lost-password', [LoginController::class, 'postLostPassword']);
        Route::post('login/first-access', [LoginController::class, 'postFirstAccess']);
    });

    Route::group(['middleware' => ['auth.admin']], function () {

        Route::post('logout', [LoginController::class, 'logout']);
        Route::post('invite', [LoginController::class, 'inviteUser']);
        Route::get('analyze/technique/{id}', [AnalyzeController::class, 'getByTechnique']);
        Route::get('analyze', [AnalyzeController::class, 'getAll']);
        Route::resource('unit', UnitController::class)->only(['index', 'show', 'store', 'destroy']);
        Route::resource('team', TeamController::class)->only(['index', 'show', 'store', 'destroy']);
        Route::resource('group', GroupController::class)->only(['index', 'show', 'store', 'destroy']);
        Route::resource('subgroup', SubgroupController::class)->only(['index', 'show', 'store', 'destroy']);
        Route::resource('question', QuestionController::class)->only(['index', 'show', 'store', 'destroy']);
        Route::resource('activity', ActivityController::class)->only(['index', 'show', 'store', 'destroy']);
        Route::resource('scheduler', SchedulerController::class)->only(['index', 'show', 'store', 'destroy']);
    });
});

// Kept for backwards compatibility with the manager web app.
Route::group(['middleware' => ['auth.admin']], function () {
    Route::get('analyze/technique/{id}', [AnalyzeController::class, 'getByTechnique']);
    Route::get('analyze', [AnalyzeController::class, 'getAll']);
});

Route::group(['prefix' => 'app'], function () {

    Route::group(['middleware' => ['throttle:auth']], function () {
        Route::post('signup', [ClientController::class, 'signup']);
        Route::post('login', [ClientController::class, 'login']);
        Route::post('lost-password', [ClientController::class, 'lostPassword']);
    });

    Route::group(['middleware' => ['auth.client']], function () {

        Route::get('group', [GroupController::class, 'index']);
        Route::get('subgroup/{id}', [SubgroupController::class, 'show']);
        Route::get('team/{id}', [TeamController::class, 'show']);
        Route::post('team-client', [TeamController::class, 'regiterForClient']);
        Route::delete('team-client', [TeamController::class, 'destroyForClient']);
        Route::get('logout', [ClientController::class, 'logout']);
        Route::post('client', [ClientController::class, 'postIndex']);
        Route::get('client', [ClientController::class, 'getIndex']);
        Route::post('update-registration', [ClientController::class, 'updateRegistration']);
        Route::post('answer', [AnswerController::class, 'postIndex']);
        Route::get('answer', [AnswerController::class, 'getIndex']);
        Route::get('scheduler', [SchedulerController::class, 'pendentByClient']);
    });
});
