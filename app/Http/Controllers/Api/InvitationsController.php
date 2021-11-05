<?php

namespace App\Http\Controllers\Api;

use App\Mail\InvitationToJoinOurSchool;
use App\Models\Invitation;
use App\Models\School;
use App\Models\SchoolUser;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class InvitationsController extends Controller
{
    /**
     * @api {post} /api/invitation/send 5. Teacher Send Invitation Email API
     * @apiName 5. Teacher Send Invitation Email API
     * @apiGroup Teacher
     * @apiVersion 0.1.0
     *
     * @apiParam {Integer} school_id School's unique ID.
     * @apiParam {String} email NEW Teacher's unique email.
     * @apiParam {String} recipient_name NEW Teacher's name.
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
     *       "message": "some validation error."
     *     }
     *
     */
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'school_id' => 'required|integer|exists:schools,id',
            'email' => 'required|email',
            'recipient_name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
            ], self::STATUS_UNAUTHORIZED);
        }

        $school = School::query()->find(request('school_id'));
        $this->authorize('run', $school);
        $user = Auth::guard('user-api')->user();
        $this->authorize('manage', $school);
        $input = $request->all();
        $input['user_id'] = $user->id;
        $random_code = Str::random();
        while (Invitation::query()
            ->where('random_code', $random_code)
            ->exists()) {
            $random_code = Str::random(8);
        }
        $input['random_code'] = $random_code;
        Invitation::query()
            ->create($input);

        if ($teacher = User::query()
            ->where('email', $input['email'])
            ->first()) {
            $teacher->update([
                'random_code' => $input['random_code'],
            ]);
        } else {
            $teacher = User::query()
                ->create([
                    'name' => $input['recipient_name'],
                    'email' => $input['email'],
                    'password' => User::PASSWORD,
                    'random_code' => $input['random_code'],
                ]);
        }

        Mail::to($teacher)->send(new InvitationToJoinOurSchool($teacher));

        return response()->json([
            'message' => 'OK.',
        ], self::STATUS_SUCCESS);
    }

    /**
     * @api {post} /api/invitation/accept 6. Teacher Accept Invitation Email API
     * @apiName 6. Teacher Accept Invitation Email API
     * @apiGroup Teacher
     * @apiVersion 0.1.0
     *
     * @apiParam {String} random_code Invitation random code.
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
     * @apiError Unauthorized Validation Error.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 401 Unauthorized
     *     {
     *       "message": "some validation error."
     *     }
     *
     */
    public function accept(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'random_code' => 'required|exists:invitations,random_code',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
            ], self::STATUS_UNAUTHORIZED);
        }

        $invitation = Invitation::query()
            ->where('random_code', request('random_code'))
            ->first();
        $user = Auth::guard('user-api')->user();
        $input['school_id'] = $invitation->school_id;
        $input['user_id'] = $user->id;
        if (SchoolUser::query()
            ->where($input)
            ->doesntExist()) {
            $input['is_founder'] = false;
            SchoolUser::query()
                ->create($input);
        }

        return response()->json([
            'message' => 'OK.',
        ], self::STATUS_SUCCESS);
    }
}
