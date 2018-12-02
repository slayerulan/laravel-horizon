<?php
namespace App\Http\Traits\Feed;

use App\FeedModel\Sport;

/**
 * this will fetch Sport name store into database
 *
 * @return array details of the event
 */
trait SportXmlFeed
{
	/**
	 * this will fetch and saved all sports
	 * 
	 * @return array details of the event
	 */
	public function fetchSports()
	{
		$url 						= FEED_URL.'o=s';
		$log_details				= [];
		$count						= 0;
		$log_details['start_time']	= date('Y-m-d H:i:s');
		$feed_object	= getObjectFromXMl($url);
		if(is_object($feed_object) && isset($feed_object->S) && count($feed_object->S)){
			//we have some data
			$insert_array	=	[];
			foreach ($feed_object->S as $key => $value) {
				$data				=	[];
				$data['id']			=	(string)$value['I'];
				$data['name']		=	(string)$value['N'];
				$data['created_at']	=	date('Y-m-d H:i:s');
				$insert_array[]	= 	$data;
				$count++;
			}
			$log_details['details']	= $count.' row inserted';
			//saved to database
			Sport::insert($insert_array);
		}else {
			$log_details['details']	= 'data not found';
		}
		$log_details['end_time']	= date('Y-m-d H:i:s');
		//log info
		$log_data 					= [];
		$log_data['event']			= 'sports feed';
		$log_data['details']		= json_encode($log_details);
		$log_data['last_timestamp']	= $feed_object->D;
		$log_data['status']			= 'ok';
		$this->logFeed($log_data);
		a($log_details);
    }
}
 ?>
