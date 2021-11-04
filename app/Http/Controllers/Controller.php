<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    const STATUS_SUCCESS = 200; // OK.
    const STATUS_UNAUTHORIZED = 401; // Unauthenticated.
    const STATUS_FORBIDDEN = 403; // Forbidden.
}
