<?php

namespace App\FeedModel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Match extends Model
{
	use SoftDeletes;

	protected $guarded 		= [];

	public function sport()
    {
    	return $this->belongsTo('App\FeedModel\Sport');
    }
	public function league()
    {
    	return $this->belongsTo('App\FeedModel\League');
    }
	public function home_team()
    {
    	return $this->belongsTo('App\FeedModel\Team','home_team_id');
    }
	public function away_team()
    {
    	return $this->belongsTo('App\FeedModel\Team','away_team_id');
    }
	public function odds()
    {
    	return $this->hasMany('App\FeedModel\Odd','match_id','match_id');
    }
    public function betslips()
    {
    	return $this->hasMany('App\Betslip');
    }
	public function default_market($bookmaker_id,$market_id)
	{
		$where	=	[
			'bookmaker_id'	=> 	$bookmaker_id,
			'market_id'	=> 	$market_id,
			'is_locked'	=> 	0
		];
		return $this->odds()->select('odds_name','odds_value','market_extra_value','id')->where($where)->get();
	}
}
