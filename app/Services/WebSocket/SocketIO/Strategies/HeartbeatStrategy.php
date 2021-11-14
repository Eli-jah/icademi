<?php
/**
 * WebSocket Heartbeat Strategy.
 */

namespace App\Services\WebSocket\SocketIO\Strategies;

use App\Services\WebSocket\SocketIO\Packet;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

class HeartbeatStrategy
{
    /**
     * If return value is true will skip decoding.
     *
     * @param Server $server
     * @param Frame  $frame
     *
     * @return boolean
     */
    public function handle(Server $server, Frame $frame)
    {
        $packet = $frame->data;
        $packetLength = strlen($packet);
        $payload = '';

        if (Packet::getPayload($packet)) {
            return false;
        }

        if ($isPing = Packet::isSocketType($packet, 'ping')) {
            $payload .= Packet::PONG;
        }

        if ($isPing && $packetLength > 1) {
            $payload .= substr($packet, 1, $packetLength - 1);
        }

        if ($isPing) {
            $server->push($frame->fd, $payload);
        }

        return true;
    }
}