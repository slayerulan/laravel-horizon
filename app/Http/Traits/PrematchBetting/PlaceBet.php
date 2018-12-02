<?php

namespace App\Http\Traits\PrematchBetting;

use Session;
use App\FeedModel\Odd;

/**
 * This will contain all functions that will help to place a pre match bet
 *
 *  @author Anirban Saha
 */
trait PlaceBet
{
	/**
	 *  this will contain pre match odd details
	 *
	 *  @var  object
	 */
	public $odd_details;
	/**
	 *  this will contain live match odd details
	 *
	 *  @var  object
	 */
	public $live_odd_details;

	/**
	 *  this will check if a odd is valid to place or not
	 *
	 *  @param  int  $odd_id  id of odds table
	 *  @return  boolean  true if valid/ false if not
	 */
	public function isValidOdd($odd_id)
	{
		$this->setOddDetails($odd_id);
		if(isset($this->odd_details->id)){
			if($this->odd_details->is_locked ==	0 &&
				$this->odd_details->match->start_timestamp >= strtotime('+'.config('bet_settings.hide_minute',0).' minutes') ) {
				return true;
			}
		}
		return false;
	}

    /**
     * Set the value of this will contail odd details
     *
     * @param int odd id
     *
     * @return self
     */
    public function setOddDetails($odd_id)
    {
		$odd_details		=	Odd::find($odd_id);
        $this->odd_details 	= 	$odd_details;

        return $this;
    }

	/**
	 *  this will set a bet details into session
	 *
	 *  @return	self
	 */
	public function setBetIntoSession()
	{
		$bet_slip_data	=	[];
		$bet_slip_data['type'] 			= 	'pre';
		$bet_slip_data['sport_id']		=	$this->odd_details->sport_id;
		$bet_slip_data['match_id']		=	$this->odd_details->match_id;
		$bet_slip_data['odds_id']		=	$this->odd_details->id;
		$bet_slip_data['market_name']	=	$this->odd_details->market->name;
		$bet_slip_data['bet_for']		=	$this->odd_details->odds_name;
		$bet_slip_data['odds_value']	=	round($this->odd_details->odds_value, 2);
		$bet_slip_data['extra_value']	=	getMarketExtraValue($this->odd_details->market->market_group, $this->odd_details->odds_name, $this->odd_details->market_extra_value, $this->odd_details->market->name);
		$bet_slip_data['home_team']		=	$this->odd_details->match->home_team->name;
		$bet_slip_data['away_team']		=	$this->odd_details->match->away_team->name;
		$selected_bets					=	Session::has('pre_match_selected_bet') ? Session::get('pre_match_selected_bet') : [];
		$unique_bet_key					=	Session::has('unique_bet_key') ? Session::get('unique_bet_key') : [];
		$unique_bet_key[$this->odd_details->id]	=	$bet_slip_data['odds_id'];
		$selected_bets[$this->odd_details->id]	=	$bet_slip_data;
		Session::put('pre_match_selected_bet', $selected_bets);
		Session::put('unique_bet_key', $unique_bet_key);

		return $this;
	}

	/**
	 *  this will unset all bets from session
	 */
	public function unsetAllBets()
	{
		Session::put('pre_match_selected_bet',[]);
		Session::put('unique_bet_key', []);
	}

	// public function getEachBetData($odd_id)
	// {
	// 	$bet_details			=	session('pre_match_selected_bet')[$odd_id];
	// 	$bet_slip_data			=	[];
	// 	if(count($bet_details)) {
	// 		$bet_slip_data['id']			=	$odd_id;
	// 		$bet_slip_data['title']			=	__('team.'.$bet_details['home_team']).' '. __('label.vs').' '. __('team.'.$bet_details['away_team']);
	// 		$bet_slip_data['odds']			=	(float)$bet_details['odds_value'];
	// 		$bet_slip_data['market_name']	=	__('markets.'.$bet_details['market_name']);
	// 		$bet_slip_data['bet_for']		= 	__('odds.'.(($bet_details['bet_for'] === '1') ? 'Home' : (($bet_details['bet_for'] === '2') ? 'Away' : $bet_details['bet_for'])) );
	// 		$bet_slip_data['extra_value']	=	$bet_details['extra_value'];
	// 	}
	// 	return $bet_slip_data;
	// }
	
	/**
	 *  this will check if a odd is valid to place or not
	 *
	 *  @param  int  $odd_id  id of odds table
	 *  @return  boolean  true if valid/ false if not
	 */
	public function isValidLiveOdd($country, $league, $match_id, $market_id, $bet_for) {
		$this->live_odd_details = $this->setLiveOddDetails($country, $league, $match_id, $market_id, $bet_for);
		// a($this->live_odd_details);
		if($this->live_odd_details != false){
			if($this->live_odd_details['suspend'] ==	0) {
				return true;
			}
		}
		return false;
	}

	/**
	 * sets the live selected odds details
	 * @param string 	$country   		country name
	 * @param string 	$league    		league name
	 * @param integer 	$match_id  		id of the match
	 * @param integer 	$market_id 		id of the market
	 * @param string 	$bet_for   		bet for
	 */
	public function setLiveOddDetails($country, $league, $match_id, $market_id, $bet_for) {
		$live_details = [];
		$live_details['country'] = $country;
		$live_details['league'] = $league;
		$live_details['match_id'] = $match_id;
		$live_details['market_id'] = $market_id;
		$live_details['bet_for'] = $bet_for;
		$url = LIVE_FEED_URL;
		$feed = getObjectFromJSON($url);
		if(is_object($feed)) {
			$feed_object = $feed->message[0];
			if (!isset($feed_object->$country->$league)) {
				return false;
			}
			$leagues = $feed_object->$country->$league;
			$leagues_collection = collect($leagues);
			$matches = $leagues_collection->keyBy('id');
			if (!isset($matches[$match_id])) {
				return false;
			}
			$live_details['home'] = $matches[$match_id]->stats->{0}->home;
			$live_details['away'] = $matches[$match_id]->stats->{0}->away;
			
			$matches_collection = collect($matches[$match_id]->odds);
			$markets = $matches_collection->keyBy('id');
			if (!isset($markets[$market_id])) {
				return false;
			}
			$live_details['market_name'] = $markets[$market_id]->name;
			$live_details['suspend'] = $markets[$market_id]->suspend;

			$market_collection = collect($markets[$market_id]->participants);
			$odds = $market_collection->keyBy('short_name');
			$live_details['odds_id'] = $odds[$bet_for]->id;
			$live_details['odds_value'] = round($odds[$bet_for]->value_eu, 2);
			$live_details['extra_value'] = $odds[$bet_for]->handicap;
			// a($live_odd_details);
			return $live_details;
		}
	}

	/**
	 *  this will set a live bet details into session
	 *
	 *  @return	self
	 */
	public function setLiveBetIntoSession()
	{
		$bet_slip_data	=	[];
		$bet_slip_data['type'] 			= 	'live';
		$bet_slip_data['sport_id']		=	50;
		$bet_slip_data['country']		=	$this->live_odd_details['country'];
		$bet_slip_data['league']		=	$this->live_odd_details['league'];
		$bet_slip_data['match_id']		=	$this->live_odd_details['match_id'];
		$bet_slip_data['odds_id']		=	$this->live_odd_details['odds_id'];
		$bet_slip_data['market_name']	=	$this->live_odd_details['market_name'];
		$bet_slip_data['market_id']		=	$this->live_odd_details['market_id'];
		$bet_slip_data['bet_for']		=	$this->live_odd_details['bet_for'];
		$bet_slip_data['odds_value']	=	round($this->live_odd_details['odds_value'], 2);
		$bet_slip_data['extra_value']	=	$this->live_odd_details['extra_value'];
		$bet_slip_data['home_team']		=	$this->live_odd_details['home'];
		$bet_slip_data['away_team']		=	$this->live_odd_details['away'];
		$selected_bets					=	Session::has('pre_match_selected_bet') ? Session::get('pre_match_selected_bet') : [];
		$unique_bet_key					=	Session::has('unique_bet_key') ? Session::get('unique_bet_key') : [];
		$unique_bet_key[$this->live_odd_details['odds_id']]	=	$bet_slip_data['odds_id'];
		$selected_bets[$this->live_odd_details['odds_id']]	=	$bet_slip_data;
		Session::put('pre_match_selected_bet', $selected_bets);
		Session::put('unique_bet_key', $unique_bet_key);
		return $this;
	}
}
