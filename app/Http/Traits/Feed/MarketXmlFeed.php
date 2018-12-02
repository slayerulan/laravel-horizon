<?php
namespace App\Http\Traits\Feed;

use App\FeedModel\Market;

/**
 * this will fetch MarketType name store into database
 *
 * @return array details of the event
 */
trait MarketXmlFeed
{
	/**
	 * this will fetch and saved all markets
	 *
	 * @return array details of the event
	 */
	public function fetchMarkets()
	{
		$url 						= FEED_URL.'o=bt';
		$log_details				= [];
		$count						= 0;
		$log_details['start_time']	= date('Y-m-d H:i:s');
		$feed_object	= getObjectFromXMl($url);
		if(is_object($feed_object) && isset($feed_object->BT) && count($feed_object->BT)){
			//we have some data
			$insert_array	=	[];
			foreach ($feed_object->BT as $key => $value) {
				$data				=	[];
				$data['sport_id']	=	(string)$value['S'];
				$data['id']			=	(string)$value['I'];
				$data['name']		=	(string)$value['N'];
				$data['market_key']	=	(string)$value['KX'];
				$data['market_group']	=	(string)$value['GRP'];
				$data['odds']		=	(string)$value['NS'];
				$data['odds']		=	(string)$value['NS'];
				$data['has_extra']	=	(string)$value['HE'];
				$data['created_at']	=	date('Y-m-d H:i:s');
				$insert_array[]	= 	$data;
				$count++;
			}
			$log_details['details']	= $count.' row inserted';
			//saved to database
			Market::insert($insert_array);
		}else {
			$log_details['details']	= 'data not found';
		}
		$log_details['end_time']	= date('Y-m-d H:i:s');
		//log info
		$log_data 					= [];
		$log_data['event']			= 'market feed';
		$log_data['details']		= json_encode($log_details);
		$log_data['last_timestamp']	= $feed_object->D;
		$log_data['status']			= (int)$feed_object->CNT == $count ? 'ok' : 'error';
		$this->logFeed($log_data);
		a($log_details);
	}
}
 ?>
