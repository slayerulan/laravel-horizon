<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\admin\Authentication;

class resetpass extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * reset password url
     * @var string
     */
    public $url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Authentication $authentication)
    {
        $this->url = $authentication->reset_pass_url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $address = 'anirban16.wgt@gmail.com';
        $name = 'APEX';
        $subject = 'Reset Password';

        return $this->markdown('view.resetpass')
                ->from($address, $name)
                //->replyTo($address, $name)
                ->subject($subject);
    }
}
