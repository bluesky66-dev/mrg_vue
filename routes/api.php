<?php

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

Route::group(['namespace' => 'Api', 'middleware' => ['auth:api']], function () {
    Route::get('profile', [
        'as'   => 'api.profile.get',
        'uses' => 'ProfileController@get',
    ]);

    Route::post('profile', [
        'as'   => 'api.profile.save',
        'uses' => 'ProfileController@save',
    ]);

    Route::get('pulse-surveys', [
        'as'   => 'api.pulse-surveys.index',
        'uses' => 'PulseSurveysController@index',
    ]);

    Route::post('pulse-surveys/{id}/resend', [
        'as'   => 'api.pulse-surveys.resend',
        'uses' => 'PulseSurveysController@resend',
    ]);

    Route::post('pulse-surveys/{id}/add', [
        'as'   => 'api.pulse-surveys.add',
        'uses' => 'PulseSurveysController@add',
    ]);

    Route::post('pulse-surveys/{id}/complete', [
        'as'   => 'api.pulse-surveys.complete',
        'uses' => 'PulseSurveysController@complete',
    ]);

    Route::get('pulse-surveys/{id}', [
        'as'   => 'api.pulse-surveys.get',
        'uses' => 'PulseSurveysController@get',
    ]);

    Route::post('pulse-surveys', [
        'as'   => 'api.pulse-surveys.save',
        'uses' => 'PulseSurveysController@save',
    ]);

    Route::post('pulse-surveys-results/{id}/delete', [
        'as'   => 'api.pulse-survey-results.delete',
        'uses' => 'PulseSurveyResultsController@delete',
    ]);

    Route::get('action-plans', [
        'as'   => 'api.action-plans.index',
        'uses' => 'ActionPlansController@index',
    ]);

    Route::post('action-plans/{id}/update', [
        'as'   => 'action-plans.update',
        'uses' => 'ActionPlansController@update',
    ]);

    Route::post('action-plans/{id}/complete', [
        'as'   => 'action-plans.complete',
        'uses' => 'ActionPlansController@complete',
    ]);

    Route::post('action-plans/{id}/share', [
        'as'   => 'action-plans.share',
        'uses' => 'ActionPlansController@share',
    ]);

    Route::post('action-plans/{id}/results/share', [
        'as'   => 'action-plans.shareResults',
        'uses' => 'ActionPlansController@shareResults',
    ]);

    Route::post('action-plans/{id}/delete', [
        'as'   => 'action-plans.delete',
        'uses' => 'ActionPlansController@delete',
    ]);

    Route::get('action-plans/{id}', [
        'as'   => 'api.action-plans.get',
        'uses' => 'ActionPlansController@get',
    ]);

    Route::post('action-plans', [
        'as'   => 'api.action-plans.save',
        'uses' => 'ActionPlansController@save',
    ]);

    Route::get('action-plan-reminders', [
        'as'   => 'api.action-plan-reminders.index',
        'uses' => 'ActionPlanRemindersController@index',
    ]);

    Route::get('action-plan-reminders/{id}', [
        'as'   => 'api.action-plan-reminders.get',
        'uses' => 'ActionPlanRemindersController@get',
    ]);

    Route::post('action-plan-reminders', [
        'as'   => 'api.action-plan-reminders.save',
        'uses' => 'ActionPlanRemindersController@save',
    ]);

    Route::get('action-steps', [
        'as'   => 'api.pulse-surveys.index',
        'uses' => 'ActionStepsController@index',
    ]);

    Route::get('action-steps/{id}', [
        'as'   => 'api.action-steps.get',
        'uses' => 'ActionStepsController@get',
    ]);

    Route::post('action-steps', [
        'as'   => 'api.action-steps.save',
        'uses' => 'ActionStepsController@save',
    ]);

    Route::get('application-states/{application_key}', [
        'as'   => 'api.application-states.get',
        'uses' => 'ApplicationStatesController@get',
    ]);

    Route::post('application-states', [
        'as'   => 'api.application-states.save',
        'uses' => 'ApplicationStatesController@save',
    ]);

    Route::get('behaviors', [
        'as'   => 'api.behaviors.index',
        'uses' => 'BehaviorsController@index',
    ]);

    Route::get('behaviors/{id}', [
        'as'   => 'api.behaviors.get',
        'uses' => 'BehaviorsController@get',
    ]);

    Route::get('cultures', [
        'as'   => 'api.cultures.index',
        'uses' => 'CulturesController@index',
    ]);

    Route::get('cultures/{id}', [
        'as'   => 'api.cultures.get',
        'uses' => 'CulturesController@get',
    ]);

    Route::get('journal-entries', [
        'as'   => 'api.journal-entries.index',
        'uses' => 'JournalEntriesController@index',
    ]);

    Route::post('journal-entries', [
        'as'   => 'api.journal-entries.save',
        'uses' => 'JournalEntriesController@save',
    ]);

    Route::post('journal-entries/share', [
        'as'   => 'api.journal-entries.share',
        'uses' => 'JournalEntriesController@share',
    ]);

    Route::post('journal-entries/{id}/delete', [
        'as'   => 'api.journal-entries.delete',
        'uses' => 'JournalEntriesController@delete',
    ]);

    Route::post('journal-entries/{id}/update', [
        'as'   => 'api.journal-entries.update',
        'uses' => 'JournalEntriesController@update',
    ]);

    Route::get('observers', [
        'as'   => 'api.observers.index',
        'uses' => 'ObserversController@index',
    ]);

    Route::get('observers/{id}', [
        'as'   => 'api.observers.get',
        'uses' => 'ObserversController@get',
    ]);

    Route::post('observers', [
        'as'   => 'api.observers.save',
        'uses' => 'ObserversController@save',
    ]);

    Route::post('observers/{id}/delete', [
        'as'   => 'api.observers.delete',
        'uses' => 'ObserversController@delete',
    ]);

    Route::post('events', [
        'as'   => 'api.events.track',
        'uses' => 'AnalyticsController@track',
    ]);

    // Media handling
    Route::resource('media', 'MediaController', ['only' => ['index', 'show', 'store']]);

});
