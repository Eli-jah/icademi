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

Route::post('passport/register', 'API\PassportController@register');
Route::post('passport/login', 'API\PassportController@login');
Route::post('passport/reset_password', 'API\PassportController@resetPassword');
Route::post('passport/logout', 'API\PassportController@logout');

Route::group(['middleware' => 'auth:user-api'], function () {
    Route::get('teacher/info', 'API\UsersController@info');
    Route::post('school/register', 'API\SchoolsController@register');
    Route::get('teacher/sent_invitations', 'API\UsersController@sentInvitations');
    Route::get('teacher/received_invitations', 'API\UsersController@receivedInvitations');
    Route::post('invitation/send', 'API\InvitationsController@send');
    Route::post('invitation/accept', 'API\InvitationsController@accept');
});

Route::group(['middleware' => 'auth:student-api'], function () {
    Route::get('student/info', 'API\StudentsController@info');
    Route::get('school/teachers', 'API\SchoolsController@teachers');
    Route::get('student/follow_teacher', 'API\StudentsController@followTeacher');
    Route::get('student/unfollow_teacher', 'API\StudentsController@unfollowTeacher');
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
