<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SchoolsController extends Controller
{
    /**
     * @api {post} /api/school/register 2. School Register API
     * @apiName 2. School Register API
     * @apiGroup Teacher
     * @apiVersion 0.1.0
     *
     * @apiParam {String} name School's unique name.
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
     * @apiSuccess {Json} response.data Json response data.
     * @apiSuccess {String} response.data.name Newly registered school name.
     * @apiSuccess {String} response.data.message Message.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "data": {
     *         "name": "some school name",
     *         "message": "OK.",
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
            'name' => 'required|unique:schools,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
            ], self::STATUS_UNAUTHORIZED);
        }

        $user = Auth::guard('user-api')->user();
        $input = $request->all();
        $input['user_id'] = $user->id;
        $school = School::query()
            ->create($input);
        $input['school_id'] = $school->id;
        $input['is_founder'] = true;
        unset($input['name']);
        SchoolUser::query()
            ->create($input);

        $data['name'] = $school->name;
        $data['message'] = 'OK.';

        return response()->json([
            'data' => $data,
        ], self::STATUS_SUCCESS);
    }

    /**
     * @api {get} /api/teacher/info 2. School Teacher List API
     * @apiName 2. School Teacher List API
     * @apiGroup Student
     * @apiVersion 0.1.0
     *
     * @apiParam {Integer} school_id School's unique ID.
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
     * @apiSuccess {Json} response.data Json response data.
     * @apiSuccess {Integer} response.data.id Teacher's unique ID.
     * @apiSuccess {String} response.data.name Teacher's name.
     * @apiSuccess {String} response.data.email Teacher's unique email.
     * @apiSuccess {Datetime} response.data.created_at Teacher's created_at.
     * @apiSuccess {Datetime} response.data.updated_at Teacher's updated_at.
     * @apiSuccess {Datetime} response.data.type User type: teacher.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "data": [
     *        {
     *          "id": 1,
     *          "name": "elijah-wang",
     *          "email": "elijah-wang@outlook.com",
     *          "created_at": "2021-11-02 15:14:13",
     *          "updated_at": "2021-11-03 05:11:30",
     *          "type": "teacher",
     *          "pivot": {
     *            "school_id": 1,
     *            "user_id": 1
     *          }
     *        },
     *        {
     *          "id": 7,
     *          "name": "teacher-no-6",
     *          "email": "teacher-email-6@test.com",
     *          "created_at": "2021-11-03 00:47:14",
     *          "updated_at": "2021-11-03 00:47:14",
     *          "type": "teacher",
     *          "pivot": {
     *            "school_id": 1,
     *            "user_id": 7
     *          }
     *        }
     *       ]
     *     }
     *
     * @apiError Unauthorized Unauthenticated or Forbidden.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 401 Unauthorized
     *     {
     *       "message": "Unauthenticated."
     *     }
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 403 Forbidden
     *     {
     *       "message": "Forbidden error message."
     *     }
     *
     */
    public function teachers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'school_id' => 'required|integer|exists:schools,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
            ], self::STATUS_UNAUTHORIZED);
        }

        $school = School::query()->find(request('school_id'));
        // $student = Auth::guard('student-api')->user();
        $this->authorize('study', $school);

        return response()->json([
            'data' => $school->teachers,
        ], self::STATUS_SUCCESS);
    }
}
