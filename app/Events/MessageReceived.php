<?php

namespace App\Events;

use App\Models\Message;
use Hhxsv5\LaravelS\Swoole\Task\Event;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Carbon;

class MessageReceived extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $message;
    protected $senderId;
    protected $senderType;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($message, $sender)
    {
        $this->message = $message;
        $this->senderId = $sender->id;
        $this->senderType = $sender->type;
    }

    /**
     * Get the message data
     *
     * return Message $message
     */
    public function getData()
    {
        $message = new Message();
        $message->conversation_id = $this->message->conversation_id;
        $message->sender_id = $this->senderId;
        $message->sender_type = $this->senderType;
        $message->content = $this->message->type == 'text' ? $this->message->content : '';
        $message->image = $this->message->type == 'image' ? $this->message->image : '';
        $message->created_at = Carbon::now()->toDateTimeString();
        return $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel | array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('icademi');
    }
}
