<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\MailBodyCreater;

/**
 * This mailable will render user forgot password mail and send it to Queue.
 *
 *  @author	Anirban Saha
 */
class FrontendForgetPasswordMail extends Mailable
{
    use Queueable, SerializesModels,MailBodyCreater;

	public $user;
	
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
		$data = [];
		$data['full_name'] = $this->user->full_name;
		$data['url'] 		= url('reset-password/'.$this->user->unique_code);
		if($this->setMailBody(2,$data)){
			Log::info('reset password mail sent to '.$this->user->id);
			return $this->markdown('emails.mail_body')->subject($this->subject);
		}
    }
}
