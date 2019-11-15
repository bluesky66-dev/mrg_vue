<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    //return view('welcome');
    return redirect('/login');
});

Route::post('login', [
    'as'   => 'auth.login.post',
    'uses' => 'AuthenticationController@login',
]);

Route::get('login', [
    'as'   => 'login',
    'uses' => 'AuthenticationController@loginForm',
]);

Route::get('magic-auth/{code}', [
    'as'   => 'auth.magic',
    'uses' => 'AuthenticationController@magicAuth',
]);

Route::post('magic-auth', [
    'as'   => 'auth.magic.send',
    'uses' => 'AuthenticationController@magicSend',
]);

Route::post('set-culture/{id}', [
    'as'   => 'utilities.set-culture',
    'uses' => 'UtilitiesController@setCulture',
]);

Route::post('logout', [
    'as'   => 'auth.logout',
    'uses' => 'AuthenticationController@logout',
]);

Route::group(['middleware' => ['web', 'auth']], function () {


    Route::get('pulse-surveys', [
        'as'   => 'pulse-surveys.index',
        'uses' => 'PulseSurveysController@index',
    ]);

    Route::get('pulse-surveys/create', [
        'as'   => 'pulse-surveys.create',
        'uses' => 'PulseSurveysController@create',
    ]);

    Route::get('pulse-surveys/{id}', [
        'as'   => 'pulse-surveys.edit',
        'uses' => 'PulseSurveysController@edit',
    ]);

    Route::get('action-plans', [
        'as'   => 'action-plans.index',
        'uses' => 'ActionPlansController@index',
    ]);

    Route::get('action-plans/create', [
        'as'   => 'action-plans.create',
        'uses' => 'ActionPlansController@create',
    ]);

    Route::get('pulse-surveys/{id}/results', [
        'as'   => 'action-plans.results',
        'uses' => 'ActionPlansController@results',
    ]);

    Route::get('action-plans/{id}/download', [
        'as'   => 'action-plans.download',
        'uses' => 'ActionPlansController@download',
    ]);

    Route::get('action-plans/{id}/results/download', [
        'as'   => 'action-plans.downloadResults',
        'uses' => 'ActionPlansController@downloadResults',
    ]);

    Route::get('action-plans/{id}/share', [
        'as'   => 'action-plans.share',
        'uses' => 'ActionPlansController@share',
    ]);

    Route::get('action-plans/{id}/complete', [
        'as'   => 'action-plans.complete',
        'uses' => 'ActionPlansController@complete',
    ]);

    Route::get('action-plans/{id}', [
        'as'   => 'action-plans.edit',
        'uses' => 'ActionPlansController@edit',
    ]);

    Route::get('journal', [
        'as'   => 'journal',
        'uses' => 'JournalEntriesController@index',
    ]);

    Route::get('dashboard', [
        'as'   => 'dashboard',
        'uses' => 'DashboardController@dashboard',
    ]);

    Route::get('profile', [
        'as'   => 'profile',
        'uses' => 'ProfileController@profile',
    ]);

    Route::get('profile/{reset}', [
        'as'   => 'profile.reset',
        'uses' => 'ProfileController@reset',
    ]);

    Route::get('reports/{id}/download', [
        'as'   => 'reports.download',
        'uses' => 'ReportsController@download',
    ]);
});

// middleware for all the PDF routes
Route::group(['prefix' => 'pdf', 'middleware' => ['auth.pdf', 'web']], function () {
    Route::get('journals/share', [
        'as'    => 'pdfs.journals.share',
        'uses' => 'JournalEntriesController@pdf',
    ]);

    Route::get('action-plans/share', [
        'as'    => 'pdfs.action-plans.share',
        'uses' => 'ActionPlansController@pdf',
    ]);

    Route::get('pulse-survey/results/share', [
        'as'    => 'pdfs.action-plans.share-results',
        'uses' => 'ActionPlansController@resultsPDF',
    ]);
});

Route::get('survey/thankyou', [
    'as'   => 'pulse-survey-result.thankyou',
    'uses' => 'PulseSurveyResultsController@thankyou',
]);

Route::post('survey/{share_code}/culture', [
    'as'   => 'pulse-survey-result.culture',
    'uses' => 'PulseSurveyResultsController@culture',
]);

Route::get('survey/{share_code}', [
    'as'   => 'pulse-survey-result.edit',
    'uses' => 'PulseSurveyResultsController@edit',
]);

Route::post('survey/{share_code}', [
    'as'   => 'pulse-survey-result.save',
    'uses' => 'PulseSurveyResultsController@save',
]);

Route::get('media/{id}', [
    'as'    => 'media.render',
    'uses'  => 'Api\\MediaController@render',
]);