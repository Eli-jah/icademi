<?php

namespace App\Services\Websocket\Facades;

use App\Services\Websocket\Conversations\ConversationContract;
use Illuminate\Support\Facades\Facade;

/**
 * @method static $this prepare()
 * @method static $this add($fd, $conversations)
 * @method static $this delete($fd, $conversations)
 * @method static array getClients($conversation)
 * @method static array getTeacher($conversation)
 * @method static array getStudent($conversation)
 * @method static array getConversations($fd)
 * @see ConversationContract
 */
class Conversation extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'swoole.conversation';
    }
}
