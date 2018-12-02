<?php

namespace App\SupportTicket;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StDepartment extends Model
{
    use SoftDeletes;
    protected $guarded 	= [];
}
