<?php

namespace App\Events;

use App\Services\Websocket\Conversations\ConversationContract;
use App\Services\WebSocket\Parser;
use App\Services\WebSocket\WebSocket;
use Hhxsv5\LaravelS\Swoole\Events\WorkerStartInterface;
use Hhxsv5\LaravelS\Swoole\Task\Event;
use Illuminate\Container\Container;
use Swoole\Http\Server;

class WorkerStartEvent  extends Event implements WorkerStartInterface
{
    public function __construct()
    {
        //
    }

    public function handle(Server $server, $workerId)
    {
        $isWebsocket = config('laravels.websocket.enable') == true;
        if (!$isWebsocket) {
            return;
        }
        // WorkerStart 事件发生时 Laravel 已经初始化完成，在这里做一些组件绑定到容器的初始化工作最合适
        app()->singleton(Parser::class, function () {
            $parserClass = config('laravels.websocket.parser');
            return new $parserClass;
        });
        app()->alias(Parser::class, 'swoole.parser');

        app()->singleton(ConversationContract::class, function () {
            $driver = config('laravels.websocket.drivers.default', 'table');
            $driverClass = config('laravels.websocket.drivers.' . $driver);
            $driverConfig = config('laravels.websocket.drivers.settings.' . $driver);
            $conversationInstance = new $driverClass($driverConfig);
            if ($conversationInstance instanceof ConversationContract) {
                $conversationInstance->prepare();
            }
            return $conversationInstance;
        });
        app()->alias(ConversationContract::class, 'swoole.conversation');

        app()->singleton(WebSocket::class, function (Container $app) {
            return new WebSocket($app->make(ConversationContract::class));
        });
        app()->alias(WebSocket::class, 'swoole.websocket');

        // 引入 Websocket 路由文件
        $routePath = base_path('routes/websocket.php');
        require $routePath;
    }
}
