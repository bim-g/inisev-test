<?php

namespace App\Mail;

use App\Models\Posts;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifyMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(object $data)
    {
        $this->data=$data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $web=Posts::find($this->data->post_id)->website;
        $link=$web->url."/".$this->data->post_id;
        return $this->from('example@example.com', 'Example')
        ->view('email.notification')
        ->with([
            'title' => $this->data->title,
            'description' => $this->data->description,
            'link' => $link,
        ]);
    }
}
