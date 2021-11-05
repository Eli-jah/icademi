<?php

use App\Admin\Controllers\SchoolsController;
use App\Admin\Controllers\StudentsController;
use App\Admin\Controllers\TeachersController;
use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');
    $router->resource('teachers', TeachersController::class);
    $router->resource('schools', SchoolsController::class);
    $router->resource('students', StudentsController::class);
});
