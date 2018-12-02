<?php

namespace App\FeedModel;

use Illuminate\Database\Eloquent\Model;

class League extends Model
{
	public $incrementing 	= false;
	protected $guarded 		= [];

	public function sport()
    {
    	return $this->belongsTo('App\FeedModel\Sport');
    }
	public function country()
    {
    	return $this->belongsTo('App\Country');
    }
	public function matches()
	{
		return $this->hasMany('App\FeedModel\Match');
	}

}
