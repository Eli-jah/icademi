<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PassportController extends Controller
{
    const STATUS_SUCCESS = 200;
    const STATUS_UNAUTHORIZED = 401;

    /**
     * login api
     *
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        // if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
        if (Auth::guard('web')->attempt(['email' => request('email'), 'password' => request('password')])) {
            // $user = Auth::user();
            $user = Auth::guard('web')->user();
            $data['token'] = $user->createToken('icademi')->accessToken;
            return response()->json([
                'data' => $data,
            ], self::STATUS_SUCCESS);
        } else {
            return response()->json([
                'error' => 'Unauthorised',
            ], self::STATUS_UNAUTHORIZED);
        }
    }

    /**
     * Register api
     *
     * @return JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ], self::STATUS_UNAUTHORIZED);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::query()
            ->create($input);
        $data['token'] = $user->createToken('icademi')->accessToken;
        $data['name'] = $user->name;

        return response()->json([
            'data' => $data,
        ], self::STATUS_SUCCESS);
    }

    /**
     * info api
     *
     * @return JsonResponse
     */
    public function info(Request $request)
    {
        // $user = Auth::user();
        $user = Auth::guard('user-api')->user();
        return response()->json([
            'data' => $user,
        ], self::STATUS_SUCCESS);
    }
}