<?php

namespace App\Services\WebSocket;

class WebSocket
{
    use Authenticatable;

    const PUSH_ACTION = 'push';
    const EVENT_CONNECT = 'connect';
    const USER_PREFIX = 'ws_uid_';

    /**
     * Determine whether to broadcast.
     *
     * @var boolean
     */
    protected $isBroadcast = false;

    /**
     * Socket sender's fd.
     *
     * @var integer
     */
    protected $sender;

    /**
     * Recipient's fd or conversation id.
     *
     * @var array
     */
    protected $to = [];

    /**
     * Websocket event callbacks.
     *
     * @var array
     */
    protected $callbacks = [];
}