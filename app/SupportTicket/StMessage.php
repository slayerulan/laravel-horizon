<?php

namespace App\SupportTicket;

use Illuminate\Database\Eloquent\Model;

class StMessage extends Model
{
    protected $guarded	=	[];

	public function support_ticket()
    {
    	return $this->belongsTo('App\SupportTicket\SupportTicket');
    }
}
