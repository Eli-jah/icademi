<?php
/**
 * WebSocket Connection Handler
 */

namespace App\Services\WebSocket;

use App\Services\WebSocket\SocketIO\Packet;
use App\Services\WebSocket\SocketIO\SocketIOParser;
use Hhxsv5\LaravelS\Swoole\WebSocketHandlerInterface;
use Illuminate\Support\Facades\Log;
use Swoole\Http\Request;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

class WebSocketHandler implements WebSocketHandlerInterface
{
    /**
     * @var WebSocket
     */
    protected $websocket;
    /**
     * @var Parser
     */
    protected $parser;

    public function __construct()
    {
        $this->websocket = app(WebSocket::class);
        $this->parser = app(SocketIOParser::class);
    }

    // on websocket connection opened
    public function onOpen(Server $server, Request $request)
    {
        if (!request()->input('sid')) {
            // websocket connection initialized. socket.io-client
            $payload = json_encode([
                'sid' => base64_encode(uniqid()),
                'upgrades' => [],
                'pingInterval' => config('laravels.swoole.heartbeat_idle_time') * 1000,
                'pingTimeout' => config('laravels.swoole.heartbeat_check_interval') * 1000,
            ]);
            $initPayload = Packet::OPEN . $payload;
            $connectPayload = Packet::MESSAGE . Packet::CONNECT;
            $server->push($request->fd, $initPayload);
            $server->push($request->fd, $connectPayload);
        }

        Log::info('WebSocket Connection Opened:' . $request->fd);
        $payload = [
            'sender' => $request->fd,
            'fds' => [$request->fd],
            'broadcast' => false,
            'assigned' => false,
            'event' => 'message',
            'message' => 'Welcome to icademi websocket chat',
        ];
        $pusher = Pusher::make($payload, $server);
        $pusher->push($this->parser->encode($pusher->getEvent(), $pusher->getMessage()));
    }

    // on websocket message received.
    public function onMessage(Server $server, Frame $frame)
    {
        // $frame->fd is client id，$frame->data is data received from client
        Log::info("Data received from client {$frame->fd}: {$frame->data}");
        if ($this->parser->execute($server, $frame)) {
            // Pass over HeartBeats
            return;
        }
        $payload = $this->parser->decode($frame);
        ['event' => $event, 'data' => $data] = $payload;
        $payload = [
            'sender' => $frame->fd,
            'fds' => [$frame->fd],
            'broadcast' => false,
            'assigned' => false,
            'event' => $event,
            'message' => $data,
        ];
        $pusher = Pusher::make($payload, $server);
        $pusher->push($this->parser->encode($pusher->getEvent(), $pusher->getMessage()));
    }

    // on websocket connection closed.
    public function onClose(Server $server, $fd, $reactorId)
    {
        Log::info('WebSocket Connection Closed:' . $fd);
    }
}