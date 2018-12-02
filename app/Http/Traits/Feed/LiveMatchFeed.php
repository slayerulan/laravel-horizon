<?php
namespace App\Http\Traits\Feed;

use App\FeedModel\LiveMatch;
use App\FeedModel\LivematchEvent;
use App\FeedModel\LivematchStat;
use App\FeedModel\Sport;
use Carbon\Carbon;

/**
 * this will fetch match details and store into database
 *
 * @return array details of the event
 *
 * @author Arijit Jana
 */
trait LiveMatchFeed
{

	public function fetchLiveMatch()
	{
		$url 							= LIVE_FEED_URL;
		$feed							= getObjectFromJSON($url);
		$full_log_data 					= [];
		$insert_count					= 0;
		$update_count					= 0;
		$same_count						= 0;
		$full_log_details				= [];
		$full_log_details['start_time']	= date('Y-m-d H:i:s');
		if(is_object($feed)) {
			// a($feed);
			$feed_object = $feed->message[0];
			$count_match = 0;
			foreach($feed_object as $country => $league_data) {
				if ($country != 'undefined') {
					foreach($league_data as $league => $match_data) {
						foreach($match_data as $data) {
							$home_team = $data->stats->{0}->home;
							$away_team = $data->stats->{0}->away;

							$livematch = LiveMatch::firstOrNew(['match_id' => $data->id]);
							$livematch->sports_id = 50;
							$livematch->country = $country;
							$livematch->league_name = str_replace($country.' ', '', $league);
							$livematch->home_team = $home_team;
							$livematch->away_team = $away_team;
							$livematch->start_timestamp = Carbon::createFromTimestamp(strtotime($data->start_date.' '.$data->start_time))->toDateTimeString();
							$livematch->minutes = $data->minute;
							$livematch->score = $data->score;
							if ($data->period == '1st Half' || $data->minute <= 45) {
								$livematch->ht_score = $data->score;
							}
							$livematch->period = $data->period;
							$livematch->state = $data->state;
							$livematch->save();
							$livematch_id = $livematch->id;

							$livematch_stat = LivematchStat::firstOrNew(['live_matchs_id' => $livematch_id]);
							$livematch_stat->goal = json_encode($data->stats->{1});
							$livematch_stat->corner = json_encode($data->stats->{2});
							$livematch_stat->yellowcard = json_encode($data->stats->{3});
							$livematch_stat->redcard = json_encode($data->stats->{4});
							$livematch_stat->throwin = json_encode($data->stats->{5});
							$livematch_stat->freekick = json_encode($data->stats->{6});
							$livematch_stat->goalkick = json_encode($data->stats->{7});
							$livematch_stat->penalty = json_encode($data->stats->{8});
							$livematch_stat->substitution = json_encode($data->stats->{9});
							$livematch_stat->save();

							if(isset($data->extra)) {
								foreach($data->extra as $extra) {
									if($extra->code == 1 || $extra->code == "") {
										$livematch_event = LivematchEvent::updateOrCreate(
											['live_matchs_id' => $livematch_id, 'code' => $extra->code, 'minute' => $extra->minute],
											['event' => $extra->value]
										);
									}
									else{
										$event = explode(" - ",$extra->value);
										$livematch_event = LivematchEvent::updateOrCreate(
											['live_matchs_id' => $livematch_id, 'code' => $extra->code, 'minute' => $extra->minute],
											['team' => str_replace(")","",str_replace("(","",$event[2])), 'event' => $event[1]]
										);
									}
								}
							}
							$count_match++;
						}
					}
				}

			}
			$full_log_details['details'] = $count_match.' match data have been updated';
		}
		else{
			$full_log_details['details'] = 'data not found';
		}

		$full_log_details['end_time']	= date('Y-m-d H:i:s');
		//log info
		$full_log_data['sport_id']		= 1;
		$full_log_data['event']			= 'Live match feed';
		$full_log_data['details']		= json_encode($full_log_details);
		$full_log_data['status']		=  'ok';
		$this->logFeed($full_log_data);
		a($full_log_data);
	}
}
 ?>
