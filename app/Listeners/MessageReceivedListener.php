<?php

namespace App\Listeners;

use App\Events\MessageReceived;
use Hhxsv5\LaravelS\Swoole\Task\Listener;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class MessageReceivedListener extends Listener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  MessageReceived  $event
     * @return void
     */
    public function handle($event)
    {
        $message = $event->getData();
        Log::info(__CLASS__ . ': 开始处理', $message->toArray());
        if ($message && $message->room_id && $message->sender_id && $message->sender_type && ($message->content || $message->image)) {
            $message->save();
            Log::info(__CLASS__ . ': 处理完毕');
        } else {
            Log::error(__CLASS__ . ': 消息字段缺失，无法保存');
        }
    }
}
