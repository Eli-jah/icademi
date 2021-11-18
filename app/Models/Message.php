<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    const SENDER_TYPE_TEACHER = 'teacher';
    const SENDER_TYPE_STUDENT = 'student';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'conversation_id',
        'sender_id',
        'sender_name',
        'sender_type',
        'content',
        'image',
    ];

    /* Eloquent Relationships */
    public function conversation()
    {
        return $this->belongsTo(Conversation::class, 'conversation_id', 'id');
    }
}
