<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Passport\Token;

class PassportController extends Controller
{
    /**
     * @api {post} /api/passport/register 1. Teacher Register API
     * @apiName 1. Teacher Register API
     * @apiGroup Passport
     * @apiVersion 0.1.0
     *
     * @apiParam {String} name Teacher's name.
     * @apiParam {String} email Teacher's unique email.
     * @apiParam {String} password Teacher's password.
     * @apiParam {String} confirm_password Confirm Teacher's password.
     *
     * @apiSuccess {Json} response Json response.
     * @apiSuccess {Json} response.data Json response data.
     * @apiSuccess {String} response.data.token Bearer Token.
     * @apiSuccess {String} response.data.ws_token Websocket Token.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "data": {
     *         "name": "some-teacher-name",
     *         "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjE2ZjBhNTFjZWI5ZmViZWRmYjQ4MmNjNTkwNjMyNDIzZTJlNDc0ZDY1ZTFjYjUyNzFjODQ1Y2Y2NWU4NDNlMWUwN2FkOTQ3NmJhYjU3YjQwIn0.eyJhdWQiOiIxIiwianRpIjoiMTZmMGE1MWNlYjlmZWJlZGZiNDgyY2M1OTA2MzI0MjNlMmU0NzRkNjVlMWNiNTI3MWM4NDVjZjY1ZTg0M2UxZTA3YWQ5NDc2YmFiNTdiNDAiLCJpYXQiOjE2MzU4MjE4NDUsIm5iZiI6MTYzNTgyMTg0NSwiZXhwIjoxNjY3MzU3ODQ1LCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.w1iPsCps7xrxbgN63aR0VQT9r86pBJz9uW3ahQURUreLsbEeV-QBYMq3PWkleKyRClTS2MxQ845zjvPZ5U2JdoJTCV1hBRp501hxbbWLAfhoFwPDtE6xs0I15Cnqu8NNB0BC0_YkSSpeogUl7Z88llHyRi36_6srl5qizZ14oxmuzLSpjQm0zIj6UGkGe3BW0eSSQte_LBZ_-p1DLrtvoB_9dXN4qs8x9IoXbcP-awMawkHp7BNsHHDZV1CEOTjzR3fkmPnLYfrgC-tBmmwEhCYvA2c5u31Kp_7bAVP1YxP-wX5twSi1fYhU_iZxS0eYf6nPY6Pmob42SCpAvb1IvcUPNg9i9krMjlOrhCUifkbM1P9agNrWpOLQSC8NBYrj2jCSvxG-1hw1u2GccW1YzF1V2kERQBamL_i06JgPXVDsRLRwZXKtBnDmYl3ZVjlgPpM4_SrLvRKgJPVAmsZBpFWF25rxtK4VShvdsK9vKxxUIhpx1-PYwIpjH7N6d3rK19PiVCM_b5X6MqhNcGNmbsK7SvtiwqHZkF7NEDjNPiPahe_ucFgZwAeKHpnDyUoHDVMQgI9CVR9xa0zPY5WNEmmEGlQtmGmTfmAXL-QknPtV_kGNGjvPwHWXQAYIflJT8Sk31BeO95uQVKybbPjwBdt6e7vWWUQveh-XaqcyCIw",
     *         "ws_token": "mXyQuVu1FtmznLX8VfbQGGcx9HmGTlRI"
     *       }
     *     }
     *
     * @apiError Unauthorized Validation Error.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 401 Unauthorized
     *     {
     *       "message": "some validation error."
     *     }
     *
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
                'message' => $validator->errors(),
            ], self::STATUS_UNAUTHORIZED);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $input['ws_token'] = Str::random(32);
        unset($input['confirm_password']);

        if ($user = User::query()
            ->where([
                'email' => $input['email'],
                'password' => User::PASSWORD,
            ])
            ->whereNotNull('random_code')
            ->first()) {
            $user->update($input);
        } else if ($user = User::query()
            ->where([
                'email' => $input['email'],
            ])
            ->where('password', '<>', User::PASSWORD)
            ->first()) {
            return response()->json([
                'message' => 'This email has already been used to register.',
            ], self::STATUS_UNAUTHORIZED);
        } else {
            $user = User::query()
                ->create($input);
        }

        $data['name'] = $user->name;
        $data['token'] = $user->createToken('icademi-teacher')->accessToken;
        $data['ws_token'] = $user->ws_token;

        return response()->json([
            'data' => $data,
        ], self::STATUS_SUCCESS);
    }

    /**
     * @api {post} /api/passport/login 2. General Login API
     * @apiName 2. General Login API
     * @apiGroup Passport
     * @apiVersion 0.1.0
     *
     * @apiParam {String} email User's unique email.
     * @apiParam {String} password User's password.
     *
     * @apiSuccess {Json} response Json response.
     * @apiSuccess {Json} response.data Json response data.
     * @apiSuccess {String} response.data.type User type: {teacher | student}.
     * @apiSuccess {String} response.data.token Bearer Token.
     * @apiSuccess {String} response.data.ws_token Websocket Token.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "data": {
     *         "id": 1,
     *         "name": "elijah",
     *         "type": "teacher",
     *         "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjE2ZjBhNTFjZWI5ZmViZWRmYjQ4MmNjNTkwNjMyNDIzZTJlNDc0ZDY1ZTFjYjUyNzFjODQ1Y2Y2NWU4NDNlMWUwN2FkOTQ3NmJhYjU3YjQwIn0.eyJhdWQiOiIxIiwianRpIjoiMTZmMGE1MWNlYjlmZWJlZGZiNDgyY2M1OTA2MzI0MjNlMmU0NzRkNjVlMWNiNTI3MWM4NDVjZjY1ZTg0M2UxZTA3YWQ5NDc2YmFiNTdiNDAiLCJpYXQiOjE2MzU4MjE4NDUsIm5iZiI6MTYzNTgyMTg0NSwiZXhwIjoxNjY3MzU3ODQ1LCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.w1iPsCps7xrxbgN63aR0VQT9r86pBJz9uW3ahQURUreLsbEeV-QBYMq3PWkleKyRClTS2MxQ845zjvPZ5U2JdoJTCV1hBRp501hxbbWLAfhoFwPDtE6xs0I15Cnqu8NNB0BC0_YkSSpeogUl7Z88llHyRi36_6srl5qizZ14oxmuzLSpjQm0zIj6UGkGe3BW0eSSQte_LBZ_-p1DLrtvoB_9dXN4qs8x9IoXbcP-awMawkHp7BNsHHDZV1CEOTjzR3fkmPnLYfrgC-tBmmwEhCYvA2c5u31Kp_7bAVP1YxP-wX5twSi1fYhU_iZxS0eYf6nPY6Pmob42SCpAvb1IvcUPNg9i9krMjlOrhCUifkbM1P9agNrWpOLQSC8NBYrj2jCSvxG-1hw1u2GccW1YzF1V2kERQBamL_i06JgPXVDsRLRwZXKtBnDmYl3ZVjlgPpM4_SrLvRKgJPVAmsZBpFWF25rxtK4VShvdsK9vKxxUIhpx1-PYwIpjH7N6d3rK19PiVCM_b5X6MqhNcGNmbsK7SvtiwqHZkF7NEDjNPiPahe_ucFgZwAeKHpnDyUoHDVMQgI9CVR9xa0zPY5WNEmmEGlQtmGmTfmAXL-QknPtV_kGNGjvPwHWXQAYIflJT8Sk31BeO95uQVKybbPjwBdt6e7vWWUQveh-XaqcyCIw",
     *         "ws_token": "mXyQuVu1FtmznLX8VfbQGGcx9HmGTlRI"
     *       }
     *     }
     *
     * @apiError Unauthorized Unauthenticated.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 401 Unauthorized
     *     {
     *       "message": "Unauthenticated."
     *     }
     *
     */
    public function login(Request $request)
    {
        // if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
        if (Auth::guard('web')->attempt(['email' => request('email'), 'password' => request('password')])) {
            // $user = Auth::user();
            $user = Auth::guard('web')->user();
            $data['id'] = $user->id;
            $data['name'] = $user->name;
            $data['type'] = 'teacher';
            $data['token'] = $user->createToken('icademi-teacher')->accessToken;
            $data['ws_token'] = Str::random(32);
            $user->update([
                'ws_token' => $data['ws_token'],
            ]);
            return response()->json([
                'data' => $data,
            ], self::STATUS_SUCCESS);
        } else if (Auth::guard('student-web')->attempt(['email' => request('email'), 'password' => request('password')])) {
            $student = Auth::guard('student-web')->user();
            $data['id'] = $student->id;
            $data['name'] = $student->name;
            $data['type'] = 'student';
            $data['token'] = $student->createToken('icademi-student')->accessToken;
            $data['ws_token'] = Str::random(32);
            $student->update([
                'ws_token' => $data['ws_token'],
            ]);
            return response()->json([
                'data' => $data,
            ], self::STATUS_SUCCESS);
        } else {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], self::STATUS_UNAUTHORIZED);
        }
    }

    /**
     * @api {post} /api/passport/reset_password 3. User Reset Password API
     * @apiName 3. User Reset Password API
     * @apiGroup Passport
     * @apiVersion 0.1.0
     *
     * @apiParam {String} type User type: {teacher | student}.
     * @apiParam {String} email User's unique email.
     * @apiParam {String} old_password User's old password.
     * @apiParam {String} new_password User's new password.
     * @apiParam {String} confirm_password Confirm User's new password.
     *
     * @apiSuccess {Json} response Json response.
     * @apiSuccess {Json} response.data Json response data.
     * @apiSuccess {String} response.data.type User type: {teacher | student}.
     * @apiSuccess {String} response.data.message Success message.
     * @apiSuccess {String} response.data.token New Bearer Token.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "data": {
     *         "type": "teacher",
     *         "message": "OK."
     *         "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjE2ZjBhNTFjZWI5ZmViZWRmYjQ4MmNjNTkwNjMyNDIzZTJlNDc0ZDY1ZTFjYjUyNzFjODQ1Y2Y2NWU4NDNlMWUwN2FkOTQ3NmJhYjU3YjQwIn0.eyJhdWQiOiIxIiwianRpIjoiMTZmMGE1MWNlYjlmZWJlZGZiNDgyY2M1OTA2MzI0MjNlMmU0NzRkNjVlMWNiNTI3MWM4NDVjZjY1ZTg0M2UxZTA3YWQ5NDc2YmFiNTdiNDAiLCJpYXQiOjE2MzU4MjE4NDUsIm5iZiI6MTYzNTgyMTg0NSwiZXhwIjoxNjY3MzU3ODQ1LCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.w1iPsCps7xrxbgN63aR0VQT9r86pBJz9uW3ahQURUreLsbEeV-QBYMq3PWkleKyRClTS2MxQ845zjvPZ5U2JdoJTCV1hBRp501hxbbWLAfhoFwPDtE6xs0I15Cnqu8NNB0BC0_YkSSpeogUl7Z88llHyRi36_6srl5qizZ14oxmuzLSpjQm0zIj6UGkGe3BW0eSSQte_LBZ_-p1DLrtvoB_9dXN4qs8x9IoXbcP-awMawkHp7BNsHHDZV1CEOTjzR3fkmPnLYfrgC-tBmmwEhCYvA2c5u31Kp_7bAVP1YxP-wX5twSi1fYhU_iZxS0eYf6nPY6Pmob42SCpAvb1IvcUPNg9i9krMjlOrhCUifkbM1P9agNrWpOLQSC8NBYrj2jCSvxG-1hw1u2GccW1YzF1V2kERQBamL_i06JgPXVDsRLRwZXKtBnDmYl3ZVjlgPpM4_SrLvRKgJPVAmsZBpFWF25rxtK4VShvdsK9vKxxUIhpx1-PYwIpjH7N6d3rK19PiVCM_b5X6MqhNcGNmbsK7SvtiwqHZkF7NEDjNPiPahe_ucFgZwAeKHpnDyUoHDVMQgI9CVR9xa0zPY5WNEmmEGlQtmGmTfmAXL-QknPtV_kGNGjvPwHWXQAYIflJT8Sk31BeO95uQVKybbPjwBdt6e7vWWUQveh-XaqcyCIw"
     *       }
     *     }
     *
     * @apiError Unauthorized Validation Error or Unauthenticated.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 401 Unauthorized
     *     {
     *       "message": "some validation error."
     *     }
     *
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:teacher,student',
            'email' => 'required|email',
            'old_password' => 'required',
            'new_password' => 'required|different:old_password',
            'confirm_password' => 'required|same:new_password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
            ], self::STATUS_UNAUTHORIZED);
        }

        // if (Auth::attempt(['email' => request('email'), 'password' => request('old_password')])) {
        if (request('type') == 'teacher' && Auth::guard('web')->attempt(['email' => request('email'), 'password' => request('old_password')])) {
            // $user = Auth::user();
            $user = Auth::guard('web')->user();
            $user->update([
                'password' => bcrypt(request('new_password')),
            ]);
            $data['type'] = 'teacher';
            $data['message'] = 'OK.';
            $data['token'] = $user->createToken('icademi-teacher')->accessToken;
            return response()->json([
                'data' => $data,
            ], self::STATUS_SUCCESS);
        } else if (request('type') == 'student' && Auth::guard('student-web')->attempt(['email' => request('email'), 'password' => request('old_password')])) {
            $student = Auth::guard('student-web')->user();
            $student->update([
                'password' => bcrypt(request('new_password')),
            ]);
            $data['type'] = 'student';
            $data['message'] = 'OK.';
            $data['token'] = $student->createToken('icademi-student')->accessToken;
            return response()->json([
                'data' => $data,
            ], self::STATUS_SUCCESS);
        } else {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], self::STATUS_UNAUTHORIZED);
        }
    }

    /**
     * @api {post} /api/passport/logout 4. General Logout API
     * @apiName 4. General Logout API
     * @apiGroup Passport
     * @apiVersion 0.1.0
     *
     * @apiHeader {String} Accept application/json.
     * @apiHeader {String} Authorization Bearer Token.
     *
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Accept": "application/json",
     *       "Authorization": "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjE2ZjBhNTFjZWI5ZmViZWRmYjQ4MmNjNTkwNjMyNDIzZTJlNDc0ZDY1ZTFjYjUyNzFjODQ1Y2Y2NWU4NDNlMWUwN2FkOTQ3NmJhYjU3YjQwIn0.eyJhdWQiOiIxIiwianRpIjoiMTZmMGE1MWNlYjlmZWJlZGZiNDgyY2M1OTA2MzI0MjNlMmU0NzRkNjVlMWNiNTI3MWM4NDVjZjY1ZTg0M2UxZTA3YWQ5NDc2YmFiNTdiNDAiLCJpYXQiOjE2MzU4MjE4NDUsIm5iZiI6MTYzNTgyMTg0NSwiZXhwIjoxNjY3MzU3ODQ1LCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.w1iPsCps7xrxbgN63aR0VQT9r86pBJz9uW3ahQURUreLsbEeV-QBYMq3PWkleKyRClTS2MxQ845zjvPZ5U2JdoJTCV1hBRp501hxbbWLAfhoFwPDtE6xs0I15Cnqu8NNB0BC0_YkSSpeogUl7Z88llHyRi36_6srl5qizZ14oxmuzLSpjQm0zIj6UGkGe3BW0eSSQte_LBZ_-p1DLrtvoB_9dXN4qs8x9IoXbcP-awMawkHp7BNsHHDZV1CEOTjzR3fkmPnLYfrgC-tBmmwEhCYvA2c5u31Kp_7bAVP1YxP-wX5twSi1fYhU_iZxS0eYf6nPY6Pmob42SCpAvb1IvcUPNg9i9krMjlOrhCUifkbM1P9agNrWpOLQSC8NBYrj2jCSvxG-1hw1u2GccW1YzF1V2kERQBamL_i06JgPXVDsRLRwZXKtBnDmYl3ZVjlgPpM4_SrLvRKgJPVAmsZBpFWF25rxtK4VShvdsK9vKxxUIhpx1-PYwIpjH7N6d3rK19PiVCM_b5X6MqhNcGNmbsK7SvtiwqHZkF7NEDjNPiPahe_ucFgZwAeKHpnDyUoHDVMQgI9CVR9xa0zPY5WNEmmEGlQtmGmTfmAXL-QknPtV_kGNGjvPwHWXQAYIflJT8Sk31BeO95uQVKybbPjwBdt6e7vWWUQveh-XaqcyCIw"
     *     }
     *
     * @apiSuccess {Json} response Json response.
     * @apiSuccess {String} response.message Success message: OK.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "message": "OK.",
     *     }
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 401 Unauthorized
     *     {
     *       "message": "Unauthenticated."
     *     }
     *
     */
    public function logout(Request $request)
    {
        if (Auth::guard('user-api')->check()) {
            $user = Auth::guard('user-api')->user();
            $user->update([
                'ws_token' => '',
            ]);
            Token::query()
                ->where('user_id', $user->id)
                ->where('name', 'icademi-teacher')
                ->update([
                    'revoked' => true,
                ]);
        } else if (Auth::guard('student-api')->check()) {
            $student = Auth::guard('student-api')->user();
            $student->update([
                'ws_token' => '',
            ]);
            Token::query()
                ->where('user_id', $student->id)
                ->where('name', 'icademi-student')
                ->update([
                    'revoked' => true,
                ]);
        } else {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], self::STATUS_UNAUTHORIZED);
        }

        // $request->session()->invalidate();

        return response()->json([
            'message' => 'OK.',
        ], self::STATUS_SUCCESS);
    }
}