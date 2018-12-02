<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\admin\Registration;
use App\Http\Traits\MailBodyCreater;

class PlayerRegistrationMail extends Mailable
{
    use Queueable, SerializesModels, MailBodyCreater;

    /**
     * reset password url
     * @var string
     */
    public $full_name;
    public $url;
    public $username;
    public $password;

    /**
     * Create a new message instance.
     *
     * @return void
     */

     public function __construct($playerRegistration)
     {
         $this->full_name = $playerRegistration->full_name;
         $this->url = $playerRegistration->url;
         $this->username = $playerRegistration->username;
         $this->password = $playerRegistration->password;
     }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        /**$address = 'anirban16.wgt@gmail.com';
        $name = 'APEX';
        $subject = __('mail.welcome email subject');

        return $this->markdown('view.player_registration')
                ->from($address, $name)
                ->replyTo($address, $name)
                ->subject($subject);*/

        $data = [];
		$data['full_name'] = $this->full_name;
		$data['url'] 		= $this->url;
        $data['username'] = $this->username;
        $data['password'] = $this->password;
		if($this->setMailBody(4,$data)){
			return $this->markdown('emails.mail_body')->subject($this->subject);
		}
    }
}
