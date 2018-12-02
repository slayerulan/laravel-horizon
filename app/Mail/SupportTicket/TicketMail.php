<?php

namespace App\Mail\SupportTicket;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\admin\SupportTicket\SupportTicketCrud;
use App\Http\Traits\MailBodyCreater;

/**
 *  This is a command to send player registration email from admin panel.
 *
 *  @author	Sourav Chowdhury
 */
class TicketMail extends Mailable
{
    use Queueable, SerializesModels, MailBodyCreater;

    /**
     * reset password url
     * @var string
     */
    public $full_name;

    /**
     * Create a new message instance.
     *
     * @return void
     */

     public function __construct($supportTicket)
     {
         $this->full_name = $supportTicket->full_name;
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
		if($this->setMailBody(5,$data)){
			return $this->markdown('emails.mail_body')->subject($this->subject);
		}
    }
}
