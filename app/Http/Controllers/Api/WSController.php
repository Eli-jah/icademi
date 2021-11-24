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
use Illuminate\Support\Str;

class WSController extends Controller
{
    /**
     * @api {post} /api/ws/contacts 1. Contact List API
     * @apiName 1. Contact List API
     * @apiGroup Websocket
     * @apiVersion 0.1.0
     *
     * @apiParam {String} ws_token Websocket Token.
     *
     * @apiHeader {String} Accept application/json.
     *
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Accept": "application/json",
     *     }
     *
     * @apiSuccess {Json} response Json response.
     * @apiSuccess {Json} response.data Json response data.
     * @apiSuccess {Integer} response.data.id Contact's unique ID.
     * @apiSuccess {String} response.data.name Contact's name.
     * @apiSuccess {String} response.data.type Contact's type.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "data": [
     *         {
     *           "id": 1,
     *           "name": "student-no-1",
     *           "type": "student"
     *         },
     *         {
     *           "id": 2,
     *           "name": "student-no-2",
     *           "type": "student"
     *         },
     *         {
     *           "id": 3,
     *           "name": "student-no-3",
     *           "type": "student"
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
        $validator = Validator::make($request->all(), [
            'ws_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
            ], self::STATUS_UNAUTHORIZED);
        }

        $data = [];
        $ws_token = $request->input('ws_token');
        // if (Auth::guard('user-api')->check()) {
        // $user = Auth::guard('user-api')->user();
        if ($user = User::query()->where('ws_token', $ws_token)->first()) {
            $school_ids = SchoolUser::query()
                ->select('school_id')
                ->where('user_id', $user->id)
                ->pluck('school_id')
                ->toArray();
            if (!empty($school_ids)) {
                $data = Student::query()
                    ->whereIn('school_id', $school_ids)
                    ->get()
                    ->toArray();
            }
            // } else if (Auth::guard('student-api')->check()) {
            // $student = Auth::guard('student-api')->user();
        } else if ($student = Student::query()->where('ws_token', $ws_token)->first()) {
            $user_ids = SchoolUser::query()
                ->select('user_id')
                ->where('school_id', $student->school_id)
                ->pluck('user_id')
                ->toArray();
            if (!empty($user_ids)) {
                $data = User::query()
                    ->whereIn('id', $user_ids)
                    ->get()
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
     * @api {post} /api/ws/chat_history 2. Chat History API
     * @apiName 2. Chat History API
     * @apiGroup Websocket
     * @apiVersion 0.1.0
     *
     * @apiParam {String} ws_token Websocket Token.
     * @apiParam {Integer} contact_id Contact's unique ID.
     *
     * @apiHeader {String} Accept application/json.
     *
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Accept": "application/json",
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
     *           "sender_name": "elijah",
     *           "content": "hello, world.",
     *           "created_at": "2021-11-17 03:47:07"
     *         },
     *         {
     *           "sender_name": "elijah-wang",
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
            'ws_token' => 'required|string',
            'contact_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
            ], self::STATUS_UNAUTHORIZED);
        }

        $data = [];
        $ws_token = $request->input('ws_token');
        $contact_id = $request->input('contact_id');
        // if (Auth::guard('user-api')->check()) {
        // $user = Auth::guard('user-api')->user();
        if ($user = User::query()->where('ws_token', $ws_token)->first()) {
            $conversation = Conversation::query()
                ->where('user_id', $user->id)
                ->where('student_id', $contact_id)
                ->first();
            // } else if (Auth::guard('student-api')->check()) {
            // $student = Auth::guard('student-api')->user();
        } else if ($student = Student::query()->where('ws_token', $ws_token)->first()) {
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
                ->where('conversation_id', $conversation->id)
                ->limit(20)
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
     * @apiParam {String} ws_token Websocket Token.
     * @apiParam {Integer} contact_id Contact's unique ID.
     * @apiParam {String} content Chat message content.
     *
     * @apiHeader {String} Accept application/json.
     *
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Accept": "application/json",
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
            'ws_token' => 'required|string',
            'contact_id' => 'required|integer',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
            ], self::STATUS_UNAUTHORIZED);
        }

        // $data = [];
        $ws_token = $request->input('ws_token');
        $contact_id = $request->input('contact_id');
        $data = [
            'sender_id' => $contact_id,
            'content' => $request->input('content'),
        ];
        // if (Auth::guard('user-api')->check()) {
        // $user = Auth::guard('user-api')->user();
        if ($user = User::query()->where('ws_token', $ws_token)->first()) {
            $data['sender_name'] = $user->name;
            $data['sender_type'] = Message::SENDER_TYPE_TEACHER;
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
            // } else if (Auth::guard('student-api')->check()) {
            // $student = Auth::guard('student-api')->user();
        } else if ($student = Student::query()->where('ws_token', $ws_token)->first()) {
            $data['sender_name'] = $student->name;
            $data['sender_type'] = Message::SENDER_TYPE_STUDENT;
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


    /**
     * @api {post} /api/ws/refresh_ws_token 4. Refresh WS Token API
     * @apiName 4. Refresh WS Token API
     * @apiGroup Websocket
     * @apiVersion 0.1.0
     *
     * @apiParam {String} ws_token Websocket Token.
     *
     * @apiSuccess {Json} response Json response.
     * @apiSuccess {Json} response.data Json response data.
     * @apiSuccess {String} response.data.id User's unique ID.
     * @apiSuccess {String} response.data.name User's name.
     * @apiSuccess {String} response.data.type User type: {teacher | student}.
     * @apiSuccess {String} response.data.ws_token Websocket Token.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "data": {
     *         "id": 1,
     *         "name": "elijah",
     *         "type": "teacher",
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
    public function refreshWsToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ws_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
            ], self::STATUS_UNAUTHORIZED);
        }

        $data = [];
        $ws_token = $request->input('ws_token');
        if ($user = User::query()->where('ws_token', $ws_token)->first()) {
            $ws_token = Str::random(32);
            $user->update([
                'ws_token' => $ws_token,
            ]);
            $data = [
                'id' => $user->id,
                'name' => $user->name,
                'type' => 'teacher',
                'ws_token' => $ws_token,
            ];
        } else if ($student = Student::query()->where('ws_token', $ws_token)->first()) {
            $ws_token = Str::random(32);
            $student->update([
                'ws_token' => $ws_token,
            ]);
            $data = [
                'id' => $student->id,
                'name' => $student->name,
                'type' => 'student',
                'ws_token' => $ws_token,
            ];
        } else {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], self::STATUS_UNAUTHORIZED);
        }

        return response()->json([
            'data' => $data,
        ], self::STATUS_SUCCESS);
    }
}