<?php

namespace App\Http\Controllers\Feed;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Traits\Feed\OddXmlFeed;
use App\Http\Traits\Feed\TeamXmlFeed;
use App\Http\Traits\Feed\SportXmlFeed;
use App\Http\Traits\Feed\MatchXmlFeed;
use App\Http\Traits\Feed\LeagueXmlFeed;
use App\Http\Traits\Feed\MarketXmlFeed;
use App\Http\Traits\Feed\CountryXmlFeed;
use App\Http\Traits\Feed\BookmakerXmlFeed;
use App\FeedModel\Match;
use App\FeedModel\Sport;
use App\FeedModel\Bookmaker;
use App\FeedModel\Market;
use App\FeedModel\MarketGroup;
use App\SystemActivityLog;
use App\Jobs\TestJob;

/**
 *  we will use this controller to fetch feed. There are few trait,
 *   which are used to parse feed and save into database.
 *
 *  @author  Anirban Saha
 */
class XmlFeed extends Controller
{
	use CountryXmlFeed, SportXmlFeed, TeamXmlFeed, BookmakerXmlFeed, MarketXmlFeed, LeagueXmlFeed, OddXmlFeed, MatchXmlFeed;

	public function __construct()
	{
		ini_set('max_execution_time', 0);
		ini_set('memory_limit','1000M');
	}
	/**
	 * this willsaved all market group
	 * @return void
	 */
	public function saveMarketGroup()
	{
		$all_sports	= Sport::select('id')->get();
		if(count($all_sports)){
			foreach ($all_sports as $each_sport) {
				$all_market_group = Market::distinct()->select('market_group')->where('sport_id',$each_sport->id)->get();
				if(count($all_market_group)){
					$insert_array	=	[];
					foreach ($all_market_group as $group) {
						$data					=	[];
						$data['sport_id']		=	$each_sport->id;
						$data['market_group']	=	$group->market_group;
						$data['created_at']	=	date('Y-m-d H:i:s');
						$insert_array[]	= 	$data;
					}
					//saved to database
					MarketGroup::insert($insert_array);
				}
			}
		}
	}
	/**
	 * this will return timestamp of last successfully fetched feed
	 *
	 * @param  string $event feed_slug
	 * @return string        timestamp
	 */
	public function getLastTimeStamp($event,$sport_id=0)
	{
		$where 				= [];
		$where['event']		=	$event;
		$where['status']	=	'ok';
		$where['sport_id']	=	$sport_id;
		$time	=	SystemActivityLog::select('last_timestamp')->where($where)->orderBy('id','desc')->first();
		if(isset($time->last_timestamp)){
				return '&tsmp='. $time->last_timestamp;
		}
		return '';
	}
	/**
	 * this will return url of marketfeed with query string
	 *
	 * @param  string 	$event    	current event
	 * @param  int 		$sport_id 	current sport id
	 * @return	string           	url
	 */
	public function getMarketUrl($event)
	{
		return $url = MARKET_FEED_URL.'frm=json&sdt='.gmdate("Y-m-d\TH:i:s",strtotime('- 24 hours')).'&sid='.$this->getActiveSports().$this->getLastTimeStamp($event).$this->getActiveBookmakers();
		// .$this->getActiveMarketGroup();
	}
	/**
	 * this will return query string for bookmakers
	 *
	 * @return string querystring
	 */
	public function getActiveBookmakers()
	{
		$where['status']	=	'active';
		$active_bookmakers	= 	Bookmaker::select('id')->where('status','active')->get()->pluck('id')->toArray();
		if(count($active_bookmakers)){
				return '&bid='.implode(',',$active_bookmakers);
		}
		return '';
	}
	/**
	 * this will return query string for market group
	 *
	 * @return string   active market group in querystring format
	 */
	public function getActiveMarketGroup()
	{
		$where['status']	=	'active';
		$active_market		= 	Market::select('market_group')->distinct()->where($where)->get()->pluck('market_group')->toArray();
		if(count($active_market)){
				return '&btgrp='.implode(',',$active_market);
		}
		return '';
	}
	/**
	 *  This will return active sports id
	 *
	 *  @author	 Anirban Saha
	 *  @return  string|bool  id seperated by ',' or false
	 */
	public function getActiveSports(){
		$all_sports	= Sport::select('id')->where('status','active')->where('fetch_feed','yes')->get()->pluck('id')->toArray();
		if(count($all_sports)){
			return implode(',',$all_sports);
		}
		return false;
	}
	public function testXml()
	{
		$x = Sport::where(['status'=>'active','fetch_feed'=>'yes'])->first();
		$x->update(['image'=> 'abcd image']);
		a($x);
	}
}
