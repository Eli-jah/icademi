<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\SchoolUser;
use App\Models\User;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WSController extends Controller
{
    /**
     * @api {get} /api/ws/contacts 1. Contact List API
     * @apiName 1. Contact List API
     * @apiGroup Websocket
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
     * @apiSuccess {Integer} response.data.id Contact's unique ID.
     * @apiSuccess {String} response.data.name Contact's name.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "data": [
     *         {
     *           "id": 1,
     *           "name": "student-no-1"
     *         },
     *         {
     *           "id": 2,
     *           "name": "student-no-2"
     *         },
     *         {
     *           "id": 3,
     *           "name": "student-no-3"
     *         }
     *       ]
     *     }
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 401 Unauthorized
     *     {
     *       "message": "Unauthenticated."
     *     }
     *
     */
    public function contacts(Request $request)
    {
        $data = [];
        if (Auth::guard('user-api')->check()) {
            $user = Auth::guard('user-api')->user();
            $school_ids = SchoolUser::query()
                ->select('school_id')
                ->where('user_id', $user->id)
                ->pluck('school_id')
                ->toArray();
            if (!empty($school_ids)) {
                $data = Student::query()
                    ->whereIn('school_id', $school_ids)
                    ->get()
                    ->only(['name', 'id'])
                    ->toArray();
            }
        } else if (Auth::guard('student-api')->check()) {
            $student = Auth::guard('student-api')->user();
            $user_ids = SchoolUser::query()
                ->select('user_id')
                ->where('school_id', $student->school_id)
                ->pluck('user_id')
                ->toArray();
            if (!empty($user_ids)) {
                $data = User::query()
                    ->whereIn('id', $user_ids)
                    ->get()
                    ->only(['name', 'id'])
                    ->toArray();
            }
        } else {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], self::STATUS_UNAUTHORIZED);
        }

        return response()->json([
            'data' => $data,
        ], self::STATUS_SUCCESS);
    }

    /**
     * @api {get} /api/ws/chat_history 2. Chat History API
     * @apiName 2. Chat History API
     * @apiGroup Websocket
     * @apiVersion 0.1.0
     *
     * @apiParam {Integer} contact_id Contact's unique ID.
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
     * @apiSuccess {Integer} response.data.content content of message.
     * @apiSuccess {Datetime} response.data.created_at sent_at of message.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "data": [
     *         {
     *           "content": "hello, world.",
     *           "created_at": "2021-11-17 03:47:07"
     *         },
     *         {
     *           "content": "hello, kitty.",
     *           "created_at": "2021-11-17 04:01:23"
     *         }
     *       ]
     *     }
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 401 Unauthorized
     *     {
     *       "message": "Unauthenticated."
     *     }
     *
     */
    public function chatHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contact_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
            ], self::STATUS_UNAUTHORIZED);
        }

        $data = [];
        $contact_id = $request->input('contact_id');
        if (Auth::guard('user-api')->check()) {
            $user = Auth::guard('user-api')->user();
            $conversation = Conversation::query()
                ->where('user_id', $user->id)
                ->where('student_id', $contact_id)
                ->first();
        } else if (Auth::guard('student-api')->check()) {
            $student = Auth::guard('student-api')->user();
            $conversation = Conversation::query()
                ->where('user_id', $contact_id)
                ->where('student_id', $student->id)
                ->first();
        } else {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], self::STATUS_UNAUTHORIZED);
        }

        if (isset($conversation) && !empty($conversation)) {
            $data = Message::query()
                ->select('content', 'created_at')
                ->where('conversation_id', $conversation->id)
                ->get();
        }

        return response()->json([
            'data' => $data,
        ], self::STATUS_SUCCESS);
    }

    /**
     * @api {post} /api/ws/chat 3. Chat API
     * @apiName 3. Chat API
     * @apiGroup Websocket
     * @apiVersion 0.1.0
     *
     * @apiParam {Integer} contact_id Contact's unique ID.
     * @apiParam {String} content chat message content.
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
     * @apiSuccess {Integer} response.data.content content of message.
     * @apiSuccess {Datetime} response.data.created_at sent_at of message.
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
    public function chat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contact_id' => 'required|integer',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
            ], self::STATUS_UNAUTHORIZED);
        }

        $contact_id = $request->input('contact_id');
        $data = [
            'sender_id' => $contact_id,
            'content' => $request->input('content'),
        ];
        if (Auth::guard('user-api')->check()) {
            $data['sender_type'] = Message::SENDER_TYPE_TEACHER;
            $user = Auth::guard('user-api')->user();
            $school_ids = SchoolUser::query()
                ->select('school_id')
                ->where('user_id', $user->id)
                ->pluck('school_id')
                ->toArray();
            if (empty($school_ids) || Student::query()
                    ->where('id', $contact_id)
                    ->whereIn('school_id', $school_ids)
                    ->doesntExist()) {
                return response()->json([
                    'message' => 'You do not have access to this contact.',
                ], self::STATUS_UNAUTHORIZED);
            }
            $conversation = Conversation::query()
                ->where('user_id', $user->id)
                ->where('student_id', $contact_id)
                ->firstOrCreate([
                    'user_id' => $user->id,
                    'student_id' => $contact_id,
                ]);
        } else if (Auth::guard('student-api')->check()) {
            $data['sender_type'] = Message::SENDER_TYPE_STUDENT;
            $student = Auth::guard('student-api')->user();
            $user_ids = SchoolUser::query()
                ->select('user_id')
                ->where('school_id', $student->school_id)
                ->pluck('user_id')
                ->toArray();
            if (empty($user_ids) || User::query()
                    ->where('id', $contact_id)
                    ->whereIn('id', $user_ids)
                    ->doesntExist()) {
                return response()->json([
                    'message' => 'You do not have access to this contact.',
                ], self::STATUS_UNAUTHORIZED);
            }
            $conversation = Conversation::query()
                ->firstOrCreate([
                    'user_id' => $contact_id,
                    'student_id' => $student->id,
                ]);
        } else {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], self::STATUS_UNAUTHORIZED);
        }

        if (isset($conversation) && !empty($conversation)) {
            $data['conversation_id'] = $conversation->id;
            Message::query()
                ->create($data);
        }

        return response()->json([
            'message' => 'OK.',
        ], self::STATUS_SUCCESS);
    }
}