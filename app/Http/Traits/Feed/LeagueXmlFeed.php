<?php
namespace App\Http\Traits\Feed;

use App\FeedModel\League;

/**
 * this will fetch league name for all sports and store into database
 *
 * @return array details of the event
 */
trait LeagueXmlFeed
{
	/**
	 * this will called from console command to store data
	 *
	 * @return void
	 */
	public function fetchLeague($sport_id)
	{
		$log_data 					= [];
		$log_data['last_timestamp']	= gmdate("Y-m-d\TH:i:s");
		$log_details				= [];
		$insert_count				= 0;
		$update_count				= 0;
		$log_details['start_time']	= date('Y-m-d H:i:s');
		$url 						= FEED_URL.'sid='.$sport_id.'&o=lg'.$this->getLastTimeStamp('league feed',$sport_id);
		$feed_object				= getObjectFromXMl($url);
		if(is_object($feed_object) && isset($feed_object->L) && count($feed_object->L)){
			$insert_array			=	[];
			foreach ($feed_object->L as $key => $value) {
				$league	= League::find((int)$value['I']);
				$data				=	[];
				$data['id']			=	(int)$value['I'];
				$data['sport_id']	=	(int)$value['S'];
				$data['country_id']	=	(int)$value['C'] == 0 ? null : (int)$value['C'];
				$data['name']		=	(string)$value['N'];
				$data['priority']	=	(int)$value['PRC'];
				if(isset($league->id) == false){
					//we will insert this data
					$data['created_at']	=	date('Y-m-d H:i:s');
					$insert_array[]		= 	$data;
					$insert_count++;
				}else {
					$data['updated_at']	=	date('Y-m-d H:i:s');
					$league->fill($data)->save();
					$update_count++;
				}
			}
			$log_details['details']	= $insert_count.' row inserted, '.$update_count.' row updated.';
			//saved to database
			League::insert($insert_array);
		}else {
			$log_details['details']	= 'data not found';
		}
		$log_details['end_time']	= date('Y-m-d H:i:s');
		//log info
		$log_data['sport_id']		= $sport_id;
		$log_data['event']			= 'league feed';
		$log_data['details']		= json_encode($log_details);
		$log_data['status']			=  'ok';
		$this->logFeed($log_data);
	}
}
 ?>
