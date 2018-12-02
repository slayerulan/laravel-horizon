<?php

namespace App\Listeners;

use App\Events\EventSendAccountActivationMail;
use App\Mail\FrontendUserRegistrationMail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

/**
 * This will send mail to new registered user.
 *
 *  @author	Anirban Saha
 */
class EventSendAccountActivationMailListener
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
     * @param  EventSendAccountActivationMail  $event
     * @return void
     */
    public function handle(EventSendAccountActivationMail $event)
    {
		if($event->user->role_id == 4){
			$to_email = $event->user->email;
			Mail::to($to_email)->queue(new FrontendUserRegistrationMail($event->user));
		}
    }
}
