<?php

namespace App\Mail\SupportTicket;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Http\Traits\MailBodyCreater;
use App\User;

class TicketClosureMail extends Mailable
{
    use Queueable, SerializesModels, MailBodyCreater;


    public $full_name;
    public $ticket_number;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($supportTicket)
    {
        $user = User::find($supportTicket->user_id);
        $this->full_name = $user->full_name;
        $this->ticket_number = $supportTicket->ticket_number;
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
        $data['ticket_number'] = $this->ticket_number;
		if($this->setMailBody(6,$data)){
			return $this->markdown('emails.mail_body')->subject($this->subject);
		}
    }
}
