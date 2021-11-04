<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StudentUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class StudentsController extends Controller
{
    /**
     * @api {get} /api/student/info 1. Student Info API
     * @apiName 1. Student Info API
     * @apiGroup Student
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
     * @apiSuccess {Json} response.data Json response data.
     * @apiSuccess {Integer} response.data.id Student's unique ID.
     * @apiSuccess {String} response.data.name Student's name.
     * @apiSuccess {String} response.data.email Student's unique email.
     * @apiSuccess {Datetime} response.data.created_at Student's created_at.
     * @apiSuccess {Datetime} response.data.updated_at Student's updated_at.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "data": {
     *         "id": 1,
     *         "name": "Elijah Wang",
     *         "email": "elijah-wang@outlook.com",
     *         "created_at": "2021-10-31 08:00:31",
     *         "updated_at": "2021-10-31 08:00:31",
     *         "type": "student"
     *       }
     *     }
     *
     */
    public function info(Request $request)
    {
        $student = Auth::guard('student-api')->user();
        return response()->json([
            'data' => $student,
        ], self::STATUS_SUCCESS);
    }

    /**
     * @api {get} /api/student/follow_teacher 3. Student Follow Teacher API
     * @apiName 3. Student Follow Teacher API
     * @apiGroup Student
     * @apiVersion 0.1.0
     *
     * @apiParam {Integer} teacher_id Teacher's unique ID.
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
     * @apiSuccess {String} response.message Success message.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "message": "OK."
     *     }
     *
     */
    public function followTeacher(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'teacher_id' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
            ], self::STATUS_UNAUTHORIZED);
        }

        $user = User::query()->find(request('teacher_id'));
        $student = Auth::guard('student-api')->user();
        $this->authorize('follow', $user);
        $input = [
            'user_id' => $user->id,
            'student_id' => $student->id,
        ];
        if (StudentUser::query()
            ->where($input)
            ->doesntExist()) {
            StudentUser::query()
                ->create($input);
        }

        return response()->json([
            'message' => 'OK.',
        ], self::STATUS_SUCCESS);
    }

    /**
     * @api {get} /api/student/unfollow_teacher 4. Student Unfollow Teacher API
     * @apiName 4. Student Unfollow Teacher API
     * @apiGroup Student
     * @apiVersion 0.1.0
     *
     * @apiParam {Integer} teacher_id Teacher's unique ID.
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
     * @apiSuccess {String} response.message Success message.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "message": "OK."
     *     }
     *
     */
    public function unfollowTeacher(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'teacher_id' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
            ], self::STATUS_UNAUTHORIZED);
        }

        $user = User::query()->find(request('teacher_id'));
        $student = Auth::guard('student-api')->user();
        $this->authorize('follow', $user);
        StudentUser::query()
            ->where([
                'user_id' => $user->id,
                'student_id' => $student->id,
            ])
            ->delete();

        return response()->json([
            'message' => 'OK.',
        ], self::STATUS_SUCCESS);
    }
}
