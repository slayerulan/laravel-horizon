<?php

namespace App\FeedModel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use App\User;

class Odd extends Model
{
	protected $guarded 		= [];

	public function sport()
    {
    	return $this->belongsTo('App\FeedModel\Sport');
    }
	public function league()
    {
    	return $this->belongsTo('App\FeedModel\League');
    }
	public function match()
	{
		return $this->belongsTo('App\FeedModel\Match','match_id','match_id');
	}
	public function bookmaker()
	{
		return $this->belongsTo('App\FeedModel\Bookmaker');
	}
	public function market()
	{
		return $this->belongsTo('App\FeedModel\Market','market_id','id');
	}

	/**
	 * Calculates the odds value according to the odds juice set by admin for a user.
	 *
	 * @param      integer  	$value  	The actual odds value set by a bookmaker
	 *
	 * @return     integer   	Changed odds value for a user.
	 */
	public function getOddsValueAttribute($value)
	{
		if(Session::get('users_odd') === null){
			return round($value, 2);
		}
		else{
			if (!empty(Session::get('users_odd'))) {
				foreach (Session::get('users_odd') as $users_odd) {
					if ((int)$users_odd['sport_id'] == (int)$this->sport_id) {
						$action_type = $users_odd['action_type'];
						$percentage = $users_odd['percentage'];
					}
					elseif ($users_odd['bookmaker_id'] == $this->bookmaker_id) {
						$action_type = $users_odd['action_type'];
						$percentage = $users_odd['percentage'];
					}
					else{
						$action_type = 'none';
					}

					// with odds editing juice increase, odds value decrease the same percentage and with juice decrease, odds increase
					if ($action_type == 'increase') {
						$odds = (100 - $percentage) / 100;
						break;
					}
					elseif ($action_type == 'decrease') {
						$odds = ($percentage + 100) / 100;
						break;
					}
					else{
						$odds = 1;
					}
				}
				return round($value * $odds, 2);
			}
			else{
				return round($value, 2);
			}
		}
	}
}
