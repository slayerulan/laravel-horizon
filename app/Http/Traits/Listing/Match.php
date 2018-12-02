<?php

namespace App\Http\Traits\Listing;

use App\FeedModel\Sport;
use App\FeedModel\Odd;
use DB;

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
	 *  @return  array  sport_details , league wise match, match_wise_odds_details index
	 */
	public function getMatches($sport, $league_ids, $time_range)
	{
		DB::connection()->enableQueryLog();
		$time_range			=	$time_range ? $time_range : config('bet_settings.maximum_hour',24*7);
		$sport_details		=	Sport::where('slug',$sport)->first();
		if(isset($sport_details->id)){
			$where	=	[
				// 'bookmaker_id'	=> 	$this->getBookmaker(),
				'market_id'	=> 	$sport_details->market_id,
				'is_locked'	=> 	0,
				'sport_id'	=> $sport_details->id
			];
			$matches	= 	$sport_details->matches()
								->whereIn('matches.league_id',$league_ids)
								->where('start_timestamp', '>=',strtotime('+'.config('bet_settings.hide_minute',0).' minutes'))
								->where('start_timestamp', '<=',strtotime('+'.$time_range.' hours'))
								->whereHas('odds', function($query) use($where){
									$query->select('id')->where($where)->limit(1);
									})
								->with('league.country','home_team','away_team')->orderBy('start_timestamp')
								->get();
			$league_wise_match_details			=	$matches->groupBy('league_id');
			$match_ids							=	$matches->unique('match_id')->pluck('match_id')->toArray();
			$odds_details						=	Odd::select('sport_id','bookmaker_id','odds_name','odds_value','market_extra_value','id','match_id')->where($where)->whereIn('match_id', $match_ids)->orderBy('bookmaker_id', 'ASC')->get()->groupBy('match_id')->map(function ($each_odds) {
					$flitered_each_odds = $each_odds->filter(function ($each_odd) use($each_odds) {
					 	return $each_odd->bookmaker_id === $each_odds[0]->bookmaker_id;
					});
					return $flitered_each_odds;
				});

			// foreach ($odds_details as $key => $value) {
			// 	$odds_collection = collect($value);
			// 	$odds_details_test[$key] = $odds_collection->keyBy('odds_name');
			// }
			// dd($odds_details_test);
			// a(DB::getQueryLog());
			$data['sport_details']				=	$sport_details;
			$data['league_wise_match_details']	=	$league_wise_match_details;
			// $data['match_wise_odds_details']	=	$odds_details_test;
			$data['match_wise_odds_details']	=	$odds_details;
			return $data;
		}else {
			abort(404);
		}
	}
}

 ?>
