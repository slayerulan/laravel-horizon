<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Betslip extends Model
{
    protected $guarded	=	[];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function sport()
    {
        return $this->belongsTo('App\FeedModel\Sport');
    }

    public function match()
    {
        return $this->belongsTo('App\FeedModel\Match','match_id','match_id');
    }

    public function live_match()
    {
        return $this->belongsTo('App\FeedModel\LiveMatch','match_id','match_id');
    }
}
