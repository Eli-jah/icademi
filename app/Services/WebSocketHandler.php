<?php

namespace App\Services;

use App\Models\User;
use App\Models\Student;
use App\Events\MessageReceived;
use Hhxsv5\LaravelS\Swoole\Task\Event;
use Hhxsv5\LaravelS\Swoole\WebSocketHandlerInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Swoole\Http\Request;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

class WebSocketHandler implements WebSocketHandlerInterface
{
    // Declare constructor without parameters.
    public function __construct()
    {
        //
    }

    // public function onHandShake(Request $request, Response $response)
    // {
    // Custom handshake: https://www.swoole.co.uk/docs/modules/swoole-websocket-server-on-handshake
    // The onOpen event will be triggered automatically after a successful handshake
    // }

    // on websocket connection opened.
    public function onOpen(Server $server, Request $request)
    {
        Log::info('WebSocket Connection Created:' . $request->fd);
    }

    // on websocket message received.
    public function onMessage(Server $server, Frame $frame)
    {
        // $frame->fd is client id，$frame->data is data received from client
        Log::info("Data received from {$frame->fd}: {$frame->data}");
        $message = json_decode($frame->data);
        $ws_token = $message->ws_token;
        // Authentication based on WS-Token.
        if (empty($ws_token) || !($user = Auth::guard('user-api')->user())) {
            Log::warning("User " . $message->name . " offline already.");
            $server->push($frame->fd, "User offline already.");
        } else {
            $user = User::query()->where('ws_token', $ws_token)->first();
            if (!$user) {
                $user = Student::query()->where('ws_token', $ws_token)->first();
            }
            if (!$user) {
                Log::warning("User " . $message->name . " offline already.");
                $server->push($frame->fd, "User offline already.");
            }
            // message received event fired.
            $event = new MessageReceived($message, $user);
            Event::fire($event);
            unset($message->ws_token);
            foreach ($server->connections as $fd) {
                if (!$server->isEstablished($fd)) {
                    continue;
                }
                $server->push($fd, json_encode($message));
            }
        }
    }

    // on websocket connection closed.
    public function onClose(Server $server, $fd, $reactorId)
    {
        Log::info('WebSocket Connection Closed:' . $fd);
    }
}