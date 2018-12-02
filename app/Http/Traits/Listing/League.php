<?php

namespace App\Http\Traits\Listing;

use Cache;
use App\FeedModel\Sport;

/**
 * This will help to show league in frontend for all sports
 *
 *  @author Anirban Saha
 */
trait League
{
	/**
	 *  This will return league of respective sport. you can filtered by country and time range
	 *
	 *  @param   string  $sport_slug  sport name
	 *  @param   null|int  $country_id  null will return all country data, id will filter
	 *  @param   null|int  $time_range  null will return default maximum range, int will filter by time
	 *  @return  Sport  sport model object
	 */
	public function getLeagues($sport, $country_id = null, $time_range = null)
	{
		$time_range		=	$time_range ? $time_range : config('bet_settings.maximum_hour',24*7);
		$cache_name 	=	$sport .'_'. $country_id .'_'. $time_range;
		$sport_details  =	Sport::where('slug',$sport)->first();
		if(isset($sport_details->id)){
			$sport_details	= Cache::remember($cache_name, 5 , function () use($sport_details, $country_id, $time_range) {
				return $sport_details->setCurrentTopLeagues($country_id, $time_range)->setCurrentLeagues($country_id, $time_range);
			});
		}else {
			abort(404);
		}
		return $sport_details;
	}
}

 ?>
