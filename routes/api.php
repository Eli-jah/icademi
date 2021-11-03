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

Route::group(['middleware' => 'auth:user-api'], function(){
    Route::get('passport/info', 'API\PassportController@info');
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
