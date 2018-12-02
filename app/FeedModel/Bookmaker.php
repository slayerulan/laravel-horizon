<?php

namespace App\FeedModel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bookmaker extends Model
{
	use SoftDeletes;

	public $incrementing 	= false;
	protected $guarded 		= [];
}
