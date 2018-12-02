<?php

namespace App\SupportTicket;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StPriority extends Model
{
    use SoftDeletes;
    protected $guarded 	= [];
}
