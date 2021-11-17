<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET,POST,OPTIONS,DELETE,PUT,PATCH');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Headers: Access-Control-Allow-Origin,Access-Control-Allow-Methods,DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type,Authorization');

        // $headers = [
        //     'Access-Control-Allow-Origin' => '*',
        //     'Access-Control-Allow-Methods' => 'GET,POST,OPTIONS,DELETE,PUT,PATCH',
        //     'Access-Control-Allow-Headers' => 'Access-Control-Allow-Origin,Access-Control-Allow-Methods,DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type,Authorization',
        // ];

        // if ($request->getMethod() == "OPTIONS") {
        //     // return Response::make('OK', 200, $headers);
        //     return response()->make('OK', 200, $headers);
        // }

        $response = $next($request);
        // foreach ($headers as $key => $value) {
        //     $response->header($key, $value);
        // }

        return $response;
    }
}