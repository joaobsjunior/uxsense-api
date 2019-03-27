<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the controller to call when that URI is requested.
  |
 */

Route::get('', function(){
    return response()->json(["API SERVER"],200);
});

Route::get('push/check', 'PushNotificationController@checkPush');
Route::get('analyze/technique/{id}', 'AnalyzeController@getByTechnique');
Route::get('analyze', 'AnalyzeController@getAll');
Route::resource('manager/technique', 'TechniqueController');

Route::group(['prefix' => 'manager'], function () {

    Route::post('login', 'LoginController@postIndex');
    Route::post('login/lost-password', 'LoginController@postLostPassword');
    Route::post('login/first-access', 'LoginController@postFirstAccess');

    Route::group(['middleware' => ['auth.admin']], function () {

        Route::post('logout', 'LoginController@logout');
        Route::post('invite', 'LoginController@inviteUser');
        Route::resource('unit', 'UnitController');
        Route::resource('team', 'TeamController');
        Route::resource('group', 'GroupController');
        Route::resource('subgroup', 'SubgroupController');
        Route::resource('question', 'QuestionController');
        Route::resource('activity', 'ActivityController');
        Route::resource('scheduler', 'SchedulerController');
    });
});

Route::group(['prefix' => 'app'], function () {

    Route::post('signup', 'ClientController@signup');
    Route::post('login', 'ClientController@login');
    Route::post('lost-password', 'ClientController@lostPassword');

    Route::group(['middleware' => ['auth.client']], function () {

        Route::get('group', 'GroupController@index');
        Route::get('subgroup/{id}', 'SubgroupController@show');
        Route::get('team/{id}', 'TeamController@show');
        Route::post('team-client', 'TeamController@regiterForClient');
        Route::delete('team-client', 'TeamController@destroyForClient');
        Route::get('logout', 'ClientController@logout');
        Route::post('client', 'ClientController@postIndex');
        Route::get('client', 'ClientController@getIndex');
        Route::post('update-registration', 'ClientController@updateRegistration');
        Route::post('answer', 'AnswerController@postIndex');
        Route::get('answer', 'AnswerController@getIndex');
        Route::get('scheduler', 'SchedulerController@pendentByClient');
    });
});

