<?php
namespace App\Http\Traits\Feed;

use App\FeedModel\Team;

/**
 * this will fetch team name for all sports and store into database
 *
 * @return array details of the event
 */
trait TeamXmlFeed
{
	/**
	 * this will called from console command to store data
	 *
	 * @return array event details
	 */
	public function fetchTeams($sport_id)
	{
		ini_set('display_errors', 1);
		$log_data 					= [];
		$log_data['last_timestamp']	= gmdate("Y-m-d\TH:i:s");
		$log_details				= [];
		$insert_count				= 0;
		$update_count				= 0;
		$log_details['start_time']	= date('Y-m-d H:i:s');
		$url 						= FEED_URL.'sid='.$sport_id.'&o=t'.$this->getLastTimeStamp('team feed',$sport_id);
		$feed_object	= getObjectFromXMl($url);
		if(is_object($feed_object) && isset($feed_object->T) && count($feed_object->T)){
			$insert_array			=	[];
			foreach ($feed_object->T as $key => $value) {
				$team	= Team::find((int)$value['I']);
				$data				=	[];
				$data['id']			=	(int)$value['I'];
				$data['sport_id']	=	(int)$value['S'];
				$data['country_id']	=	(int)$value['C'] == 0 ? null : (int)$value['C'];
				$data['name']		=	(string)$value['N'];
				$data['local_name']	=	(string)$value['N'];
				if(isset($team->id) == false){
					//we will insert this data
					$data['created_at']	=	date('Y-m-d H:i:s');
					$insert_array[]		= 	$data;
					$insert_count++;
				}else {
					if($team->sport_id != $data['sport_id'] || $team->country_id != $data['country_id'] || $team->name != $data['name'] || $team->local_name != $data['local_name']) {
						$data['updated_at']	=	date('Y-m-d H:i:s');
						$team->fill($data)->save();
						$update_count++;
					}
				}
				if($insert_count == 5000){
					foreach (array_chunk($insert_array,1000) as $rows) {
						//saved to database
						Team::insert($rows);
					}
					$insert_array	=	[];
					$insert_count	=	0;
				}
			}
			$log_details['details']	= $insert_count.' row inserted, '.$update_count.' row updated.';
			foreach (array_chunk($insert_array,1000) as $rows) {
				//saved to database
				Team::insert($rows);
			}
		}else {
			$log_details['details']	= 'data not found';
		}
		$log_details['end_time']	= date('Y-m-d H:i:s');
		//log info
		$log_data['sport_id']		= $sport_id;
		$log_data['event']			= 'team feed';
		$log_data['details']		= json_encode($log_details);
		$log_data['status']			=  'ok';
		$this->logFeed($log_data);
	}
}
 ?>
