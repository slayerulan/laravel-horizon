<?php
namespace App\FeedModel;

use Illuminate\Database\Eloquent\Model;

class Market extends Model
{
	public $incrementing 	= false;
	protected $guarded 		= [];

	public function sport()
    {
    	return $this->belongsTo('App\FeedModel\Sport');
    }
}
