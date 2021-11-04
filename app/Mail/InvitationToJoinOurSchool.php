<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class InvitationToJoinOurSchool extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected $user = false;
    // public $subject = '';

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, string $subject = '')
    {
        $this->user = $user;
        if ($subject !== '') {
            $this->subject = $subject;
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // return $this->view('emails.invitation');
        if ($this->user) {
            return $this->subject($this->subject)->view('emails.invitation', [
                'user' => $this->user,
            ]);
        }
        return false;
    }
}
