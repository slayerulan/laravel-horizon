<?php

namespace App\FeedModel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MarketGroup extends Model
{
	use SoftDeletes;

	protected $guarded 		= [];

	public function sport()
    {
    	return $this->belongsTo('App\FeedModel\Sport');
    }
}
