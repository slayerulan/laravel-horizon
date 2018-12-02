<?php

namespace App\FeedModel;

use Illuminate\Database\Eloquent\Model;

class LiveMatch extends Model
{
    protected $guarded = [];

    public function betslips()
    {
    	return $this->hasMany('App\Betslip');
    }
}
