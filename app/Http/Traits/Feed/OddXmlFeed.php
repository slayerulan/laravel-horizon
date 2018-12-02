<?php
namespace App\Http\Traits\Feed;

use App\FeedModel\Odd;

/**
 * this will fetch team name for all sports and store into database
 *
 * @return array details of the event
 */
trait OddXmlFeed
{
	/**
	 * this will called from console command to store data
	 *
	 * @return void
	 */
	public function fetchOdds()
	{
		$url 							= $this->getMarketUrl('odds feed');
		$feed_object					= getObjectFromJSON($url);
		$full_log_data 					= [];
		$full_log_data['last_timestamp']= gmdate("Y-m-d\TH:i:s");
		$full_log_details				= [];
		$full_log_details['start_time']	= date('Y-m-d H:i:s');
		if(is_object($feed_object) && isset($feed_object->S) && count($feed_object->S)) {
			foreach ($feed_object->S as $each_sport) {
				$insert_array				=	[];
				$log_data 					= [];
				$log_data['last_timestamp']	= gmdate("Y-m-d\TH:i:s");
				$log_details				= [];
				$insert_count				= 0;
				$update_count				= 0;
				$same_count					= 0;
				$log_details['start_time']	= date('Y-m-d H:i:s');

				$sport_id 	=	(int)$each_sport->I;
				foreach ($each_sport->C as $each_country) {
					if(isset($each_country->L) && count($each_country->L)){
						foreach ($each_country->L as $each_league) {
							$league_id		=	$each_league->I;
							if(isset($each_league->E) && count($each_league->E)){
								foreach ($each_league->E as $each_match) {
									$match_id	=	$each_match->I;
									if(count($each_match->M)) {
										foreach ($each_match->M as  $each_market) {
											$market_id			=	$each_market->I;
											$market_extra_value	=	$each_market->H;
											foreach ($each_market->B as  $each_bet) {
												$bookmaker_id		=	$each_bet->I;
												$bet_date_timestamp	=	$each_bet->BTDT;
												$is_locked			=	isset($each_bet->LOCKED) ? $each_bet->LOCKED : 0;
												foreach ($each_bet->O as $each_odd) {
													$data	=	[];
													$data['sport_id']			=	(int)$sport_id;
													$data['league_id']			=	(int)$league_id;
													$data['match_id']			=	(int)$match_id;
													$data['bookmaker_id']		=	(int)$bookmaker_id;
													$data['market_id']			=	(int)$market_id;
													$data['odds_name']			=	(string)$each_odd->N;
													$data['market_extra_value']	=	(string)$market_extra_value;
													$unique_key	= $data['sport_id'].'-'.$data['league_id'].'-'.$data['match_id'].'-'.$data['bookmaker_id'].'-'.$data['market_id'].'-'.$data['market_extra_value'].'-'.$data['odds_name'];
													// checking for existing entry
													$odds	=	Odd::select('id','odds_value','is_locked')->where($data)->first();
													$data['odds_value']			=	(string)$each_odd->V;
													$data['bet_timestamp']		=	(string)$bet_date_timestamp;
													$data['is_locked']			=	(int)$is_locked;
													if(isset($odds->id) == false) {
														$data['created_at']	=	date('Y-m-d H:i:s');
														$insert_array[$unique_key]		= 	$data;
														$insert_count++;
													}else if($odds->odds_value != $data['odds_value'] || $odds->is_locked != $data['is_locked']) {
														$update 				= 	[];
														$update['odds_value']	=	$data['odds_value'];
														$update['bet_timestamp']=	$data['bet_timestamp'];
														$update['is_locked']	=	$data['is_locked'];
														$odds->update($update);
														$update_count++;
													}else {
														$same_count++;
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
				$log_details['details']	=  $insert_count.' rows inserted, '.$update_count.' rows updated.'.$same_count.' rows remained same.';
				foreach (array_chunk($insert_array,1000) as $rows) {
					//saved to database
					Odd::insert($rows);
				}
				$log_details['end_time']	= date('Y-m-d H:i:s');
				//log info
				$log_data['sport_id']		= $sport_id;
				$log_data['event']			= 'odds feed';
				$log_data['details']		= json_encode($log_details);
				$log_data['status']			=  'ok';
				$this->logFeed($log_data);
			}
		}
		$full_log_details['end_time']	= date('Y-m-d H:i:s');
		//log info
		$full_log_data['sport_id']		= 0;
		$full_log_data['event']			= 'odds feed';
		$full_log_data['details']		= json_encode($full_log_details);
		$full_log_data['status']		=  'ok';
		$this->logFeed($full_log_data);
	}
}
