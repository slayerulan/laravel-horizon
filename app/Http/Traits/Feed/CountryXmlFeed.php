<?php
namespace App\Http\Traits\Feed;

use App\Country;

/**
 * this will fetch Country name for all sports and store into database
 *
 *  @author Anirban Saha
 */
trait CountryXmlFeed
{
	/**
	 * this will fetch all countries and store them in table
	 *
	 * @return array details of the event
	 */
    public function fetchCountries()
    {
    	$url 						= FEED_URL.'o=c';
		$log_details				= [];
		$count						= 0;
		$log_details['start_time']	= date('Y-m-d H:i:s');
		$feed_object	= getObjectFromXMl($url);
		if(is_object($feed_object) && isset($feed_object->C) && count($feed_object->C)){
			//we have some data
			$insert_array	=	[];
			foreach ($feed_object->C as $key => $value) {
				$check	=	Country::find((string)$value['I']);
				if(isset($check->id) == false){
					$data			=	[];
					$data['id']		=	(string)$value['I'];
					$data['name']	=	(string)$value['N'];
					$insert_array[]	= 	$data;
					$count++;
				}
			}
			$log_details['details']	= $count.' row inserted';
			//saved to database
			Country::insert($insert_array);
		}else {
			$log_details['details']	= 'data not found';
		}
		$log_details['end_time']	= date('Y-m-d H:i:s');
		//log info
		$log_data 					= [];
		$log_data['event']			= 'country feed';
		$log_data['details']		= json_encode($log_details);
		$log_data['last_timestamp']	= $feed_object->D;
		$log_data['status']			= 'ok';
		$this->logFeed($log_data);
    }
}
 ?>
