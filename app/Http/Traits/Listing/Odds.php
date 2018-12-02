<?php

namespace App\Http\Traits\Listing;

use App\FeedModel\Market;
use App\FeedModel\Odd;
use DB;

/**
 * This will help to show extra odds in frontend for all sports
 *
 *  @author Anirban Saha
 */
trait Odds
{
	/**
	 *
	 *  @param   int  $sport_id  sport id
	 *  @param   int  $match_id  match id of selected match
	 *  @return  Odd  odd model object
	 */
	public function getExtraOdds($sport_id, $match_id)
	{
		$where		=	[
					'sport_id'	=> $sport_id,
					'match_id'	=> $match_id,
					// 'bookmaker_id'	=> $this->getBookmaker(),
					'is_locked'	=> 0
				];
		$extra_odds	=	Odd::where($where)->whereHas('market', function($query){
							$query->select('id')->where('status','active');
						})->whereHas('match', function($query){
							$query->select('id')
							->where('start_timestamp', '>=',strtotime('+'.config('bet_settings.hide_minute',0).' minutes'))
							->where('start_timestamp', '<=',strtotime('+'.config('bet_settings.maximum_hour',24*7).' hours'));
						})->orderBy('bookmaker_id', 'ASC')
						->get()->groupBy('market_id')->mapToGroups(function ($item, $key) {
							
							$odds_array = $item->groupBy('market_extra_value');

							$odds_array = $odds_array->map(function ($each_odds) {

								$flitered_each_odds = $each_odds->filter(function ($each_odd) use($each_odds) {
								 	return $each_odd->bookmaker_id === $each_odds[0]->bookmaker_id;
								});

								return $flitered_each_odds;
							});

							return [$key => $odds_array->all()];
						});

		$market_ids				=	$extra_odds->keys()->toArray();
		$market_details			=	Market::select('id','name','has_extra','market_group')->whereIn('id',$market_ids)->orderBy('name')->get();
		$data					=	[];
		$data['extra_odds']		=	$extra_odds;
		$data['market_details']	=	$market_details;
		return $data;
	}
}
