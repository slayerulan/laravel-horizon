<?php
namespace App\Http\Traits\Feed;

use App\FeedModel\Match;

/**
 * this will fetch match details and store into database
 *
 * @return array details of the event
 */
trait MatchXmlFeed
{
	/**
	 * this will called from console command to store data
	 *
	 * @return void
	 */
	public function fetchMatches()
	{
		$url 							= $this->getMarketUrl('match feed');
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
									$match_id					=	$each_match->I;
									$data						=	[];
									$data['sport_id']			=	(int)$sport_id;
									$data['match_id']			=	(int)$match_id;
									//checking for existing entry
									$matches	=	Match::select('*')->where($data)->first();

									$data['league_id']			=	(int)$league_id;
									$data['start_timestamp']	=	(string)$each_match->DT;
									$data['home_team_id']		=	(int)$each_match->T1I;
									$data['away_team_id']		=	(int)$each_match->T2I;
									$data['period']				=	(string)$each_match->PR;
									$data['minute']				=	(string)$each_match->MN;
									$data['approved']			=	(int)$each_match->A;
									$data['score']				=	(string)$each_match->SC;
									$data['event_details']		=	(string)$each_match->SCE;
									if(isset($each_match->RID)){
										Match::where('match_id',(int)$each_match->RID)->where('sport_id',(int)$sport_id)->delete();
									}
									if(isset($matches->id) == false) {
										$data['created_at']		=	date('Y-m-d H:i:s');
										$insert_array[]			= 	$data;
										$insert_count++;
									}else if($matches->start_timestamp != $data['start_timestamp'] || $matches->period != $data['period'] ||$matches->minute != $data['minute'] ||$matches->approved != $data['approved'] ||$matches->score != $data['score'] ||$matches->event_details != $data['event_details']) {
										$matches->update($data);
										$update_count++;
									}else {
										$same_count++;
									}
								}
							}
						}
					}
				}
				$log_details['details']	=  $insert_count.' rows inserted, '.$update_count.' rows updated.'.$same_count.' rows remained same.';

				foreach (array_chunk($insert_array,1000) as $rows) {
					//saved to database
					Match::insert($rows);
				}
				$log_details['end_time']	= date('Y-m-d H:i:s');
				//log info
				$log_data['sport_id']		= $sport_id;
				$log_data['event']			= 'match feed';
				$log_data['details']		= json_encode($log_details);
				$log_data['status']			=  'ok';
				$this->logFeed($log_data);
			}
		}
		$full_log_details['end_time']	= date('Y-m-d H:i:s');
		//log info
		$full_log_data['sport_id']		= 0;
		$full_log_data['event']			= 'match feed';
		$full_log_data['details']		= json_encode($full_log_details);
		$full_log_data['status']		=  'ok';
		$this->logFeed($full_log_data);
	}
}
 ?>
