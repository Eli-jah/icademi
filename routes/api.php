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

Route::post('passport/register', 'Api\PassportController@register');
Route::post('passport/login', 'Api\PassportController@login');
Route::post('passport/reset_password', 'Api\PassportController@resetPassword');
Route::post('passport/logout', 'Api\PassportController@logout');

Route::group(['middleware' => 'auth:user-api'], function () {
    Route::get('teacher/info', 'Api\UsersController@info');
    Route::post('school/register', 'Api\SchoolsController@register');
    Route::get('teacher/sent_invitations', 'Api\UsersController@sentInvitations');
    Route::get('teacher/received_invitations', 'Api\UsersController@receivedInvitations');
    Route::post('invitation/send', 'Api\InvitationsController@send');
    Route::post('invitation/accept', 'Api\InvitationsController@accept');
    Route::get('school/all_students', 'Api\SchoolsController@allStudents');
    Route::get('school/fans_students', 'Api\SchoolsController@fansStudents');
});

Route::group(['middleware' => 'auth:student-api'], function () {
    Route::get('student/info', 'Api\StudentsController@info');
    Route::get('school/teachers', 'Api\SchoolsController@teachers');
    Route::get('student/follow_teacher', 'Api\StudentsController@followTeacher');
    Route::get('student/unfollow_teacher', 'Api\StudentsController@unfollowTeacher');
});

Route::get('ws/contacts', 'Api\WSController@contacts');
Route::get('ws/chat_history', 'Api\WSController@chatHistory');
Route::post('ws/chat', 'Api\WSController@chat');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
