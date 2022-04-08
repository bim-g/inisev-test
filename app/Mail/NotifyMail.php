<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifyMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $userInfos;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(object $userInfos)
    {
        $this->userInfos=$userInfos;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('example@example.com', 'Example')
        ->view('email.notification')
        ->with([
            "title"=>$this->userInfos->title,
            "description"=>$this->userInfos->description,
            "link"=>$this->userInfos->link,
        ]);
    }
}
