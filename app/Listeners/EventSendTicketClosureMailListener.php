<?php

namespace App\Listeners;

use App\Events\EventSendTicketClosureMail;
use App\Mail\SupportTicket\TicketClosureMail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class EventSendTicketClosureMailListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  EventSendTicketClosureMail  $event
     * @return void
     */
    public function handle(EventSendTicketClosureMail $event)
    {
        if($event->ticketData->st_status_type_id == 2)
        {
			$to_email = $event->user->email;
			Mail::to($to_email)->queue(new TicketClosureMail($event->ticketData));
		}
    }
}
