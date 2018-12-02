<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\SupportTicket\SupportTicket;
use App\User;

class EventSendTicketClosureMail
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $ticketData;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(SupportTicket $supportTicket)
    {
        $this->ticketData = $supportTicket;

        $user = User::find($supportTicket->user_id);
        $this->user = $user;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
