<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Http\Traits\MailBodyCreater;
/**
 * This mailable will render user registration mail and send it to Queue.
 * 
 *  @author	Anirban Saha
 */
class FrontendUserRegistrationMail extends Mailable
{
    use Queueable, SerializesModels,MailBodyCreater;

	public $full_name;
	public $url;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->full_name = $user->full_name;
		$url = url('active-my-account/'.$user->unique_code);
        $this->url = $url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
		$data = [];
		$data['full_name'] = $this->full_name;
		$data['url'] 		= $this->url;
		if($this->setMailBody(1,$data)){
			return $this->markdown('emails.mail_body')->subject($this->subject);
		}
    }
}
