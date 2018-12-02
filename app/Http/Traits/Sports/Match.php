<?php

namespace App\Http\Traits\Sports;

use Cache;
use App\FeedModel\Sport;

/**
 * This will help to show match in frontend for all sports
 *
 *  @author Anirban Saha
 */
trait Match
{
	/**
	 *  This will return matches of respective sport. you can filtered by league and time range
	 *
	 *  @param   string  $sport_slug  sport name
	 *  @param   null|int  $country_id  null will return all country data, id will filter
	 *  @param   null|int  $time_range  null will return default maximum range, int will filter by time
	 *  @return  Sport  sport model object
	 */
	public function getMatches($sport, $league_ids, $time_range)
	{
		$time_range		=	$time_range ? $time_range : config('bet_settings.maximum_hour',24*7);
		$sport_details=	Sport::where('slug',$sport)->first()->matches()
								->whereIn('league_id',$league_ids)
								->where('start_timestamp', '>=',strtotime('+'.config('bet_settings.hide_minute',0).' minutes'))
								->where('start_timestamp', '<=',strtotime('+'.$time_range.' hours'))
								->whereHas('odds', function($query){
									$query->where('is_locked', 0)
									->where('bookmaker_id', config('bet_settings.default_bookmaker',43));
								})
								->get();
		if(isset($sport_details->id)){
			$sport_details	= $sport_details->setCurrentTopLeagues($country_id, $time_range)->setCurrentLeagues($country_id, $time_range);
		}else {
			abort(404);
		}
		return $sport_details;
	}
}

 ?>
