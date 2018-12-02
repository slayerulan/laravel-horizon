<?php

namespace App\Http\Controllers\BetCalculations;

use Illuminate\Http\Request;
use App\Betslip;
use App\FeedModel\Match;
/**
 * This will control the calculation of all markets for Handball
 *
 * @author Arijit Jana
 */
class HandballCalculation extends BetCalculation
{
	/**
	 * name of all the markets which can only be calculated after match is finished
	 * @var array
	 */
	public $fulltime_odds				= ['1x2', 'Double Chance', 'Half Time/Full Time', 'Highest Scoring Half', 'Over/Under', 'Home Team Total Odd/Even', 'Away Team Total Odd/Even', 'Odd/Even', 'Odd/Even (2nd Half)', 'Asian Handicap', 'Asian Handicap (2nd Half)'];

	/**
	 * name of all the markets which can be calculated if first half is over
	 * @var array
	 */
	public $first_half_odds 	      	= ['Asian Handicap (1st Half)', 'Odd/Even (1st Half)'];
	
	/**
	 * period value that indicates the match is over
	 * @var array
	 */
	public $fulltime_match_status   	= ['255'];
	
	/**
	 * period value that indicates the match hasn't started yet
	 * @var array
	 */
	public $pre_halftime_match_status   = ['0', '-1', '1'];

	/**
	 * invalied score type indicating values
	 * @var array
	 */
    public $not_valid_score_type    	= ['','[-]','[ - ]','[]','?','[?-?]','[? - ?]','? - ?'];
	
	/**
	 * home team score for a particular match
	 * @var integer
	 */
	public $home_score;

	/**
	 * away team score for a particular match
	 * @var integer
	 */
    public $away_score;
	
	/**
	 * calculates all single bets for Handball
	 * @return string     returns the time of the calculation
	 */
    public function handballSingleBetCalculate(){
		$this->singleBetCalculation('Handball');
	}

	/**
	 * calculates all combo bets for Handball
	 * @return string     returns the time of the calculation
	 */
	public function handballComboBetCalculate(){
		$this->comboBetCalculation('Handball');
	}
	
	/**
	 * this will check match status to calculate bet
	 * @return boolean     true = do calculate / false = don't calculate
	 */
	public function checkMatchStatus(){
		if($this->checkBetCancelStatus() == true && $this->match_info != false && !in_array($this->match_info->period,$this->cancelled_match_status)){
			// now we should check match status
			if(in_array($this->match_info->period,$this->fulltime_match_status)){
				$this->calculate_bet = true;
			}
			else if(in_array($this->match_info->period,$this->pre_halftime_match_status)){
				$this->calculate_bet = false;
			}
			else if(!in_array($this->match_info->period,$this->pre_halftime_match_status)){
				// we should calculate first half odds
				if((int)$this->odds_set < 2){
					$this->calculate_bet = true;
				}
				else{
					$this->calculate_bet = false;
				}
			}
			else{
				$this->calculate_bet = false;
			}
		}
		else if($this->checkBetCancelStatus() == true && in_array($this->match_info->period,$this->cancelled_match_status)){
			// should wait for 3 hours before cancel this match
			$this->checkCancelMatchStatus();
		}
		else{
			// either no result found or cancel match
			$this->checkAndCancelMatch();
		}
	}
	
	/**
	 * this will check if a match is canceled
	 * @return boolean     true = not canceled / false = canceled or no match data found
	 */
	public function checkBetCancelStatus(){
		// this will check match either cancel or not to calculate bet
		$match_id = $this->single_bet_info->match_id;
		// $match_details = getAllSelectedValue('*',HANDBALL_MATCH_SCORE,'match_id LIKE "'.$match_id.'"');
		$match_details = Match::where('match_id', $match_id)->where('approved', 1)->first();
		if($match_details){
			$this->no_data_found = false;
			$this->match_info = $match_details;
			$this->getOddsSetNumber();
			if($this->match_info->status == "active"){
				return true;
			}
			else if($this->match_info->status == "cancel"){
				return false;
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
	}
	
	/**
	 * this will set the odds set number
	 * @return integer     odds set number indicates in which period of the match does the market belongs
	 */
	public function getOddsSetNumber(){
		$market_name = $this->single_bet_info->market_name;
		if(in_array($market_name,$this->first_half_odds)){
			$this->odds_set = 1;
		}
		else if(in_array($market_name,$this->fulltime_odds)){
			$this->odds_set = 2;
		}
		else{
			$this->odds_set = 5;
		}
	}
	
	/**
	 * this will set score of home or away for a match for the given period
	 * @param string 	$period 	period of the match of whos result is needed
	 */
	public function setScore($half = null){
        if($half == "st"){
            // need to set only second half score
			$scores = explode(" ",$this->match_info->score);
            $both_set_scores = str_replace(")","",str_replace("(","",$scores[1]));
            $each_set_scores = explode(',', $both_set_scores);
			$score = $each_set_scores[1];
        }
		elseif($half == "ht"){
			// only half time
			$scores = explode(" ",$this->match_info->score);
			$both_set_scores = str_replace(")","",str_replace("(","",$scores[1]));
			$each_set_scores = explode(',', $both_set_scores);
			$score = $each_set_scores[0];
		}
		else{
			// only full time
			$scores = explode(" ",$this->match_info->score);
			$score = $scores[0];
		}
		$score_array = explode(':', $score);
		$this->home_score = (int)$score_array[0];
		$this->away_score = (int)$score_array[1];
    }
	
	/**
	 * this will calculate each market
	 */
	public function CalculateEachBet() {
		$each_bet = $this->single_bet_info;
		
		// Double Chance
		if($each_bet->odds_type == "Double Chance"){
			$this->setScore();
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateDoubleChance($home,$away);
		}
		
		// Odd/Even
		if($each_bet->odds_type == "Odd/Even"){
			$this->setScore();
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateOddEven($home,$away);
		}
		
		// Odd/Even (1st Half)
		if($each_bet->odds_type == "Odd/Even (1st Half)"){
			$this->setScore('ht');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateOddEven($home,$away);
		}
		
		// Odd/Even (2nd Half)
		if($each_bet->odds_type == "Odd/Even (2nd Half)"){
			$this->setScore('st');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateOddEven($home,$away);
		}
		
		// Home Team Total Odd/Even
		if($each_bet->odds_type == "Home Team Total Odd/Even"){
			$this->setScore();
			$home = (int)$this->home_score;
			$this->calculateHomeOddEven($home);
		}
		
		// Away Team Total Odd/Even
		if($each_bet->odds_type == "Away Team Total Odd/Even"){
			$this->setScore();
			$away = (int)$this->home_score;
			$this->calculateAwayOddEven($away);
		}
		
		// Asian Handicap
		if($each_bet->odds_type == "Asian Handicap"){
			$this->setScore();
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateAsianHandicap($home,$away);
		}
		
		// Over/Under
		if($each_bet->odds_type == "Over/Under"){
			$this->setScore();
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateOverUnder($home,$away);
		}
		
		// Asian Handicap (1st Half)
		if($each_bet->odds_type == "Asian Handicap (1st Half)"){
			$this->setScore('ht');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateAsianHandicap($home,$away);
		}
		
		// Asian Handicap (2nd Half)
		if($each_bet->odds_type == "Asian Handicap (2nd Half)"){
			$this->setScore('st');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateAsianHandicap($home,$away);
		}
		
		// Highest Scoring Half
		if($each_bet->odds_type == "Highest Scoring Half"){
			$this->setScore('ht');
			$first_half = (int)$this->home_score + (int)$this->away_score;
			$this->setScore('st');
			$second_half = (int)$this->home_score + (int)$this->away_score;
			$this->calculateHighestScoringHalf($first_half,$second_half);
		}
		
		// Half Time/Full Time
		if($each_bet->odds_type == "Half Time/Full Time"){
			$this->setScore('ht');
			$ht_home = (int)$this->home_score;
			$ht_away = (int)$this->away_score;
			$this->setScore('st');
			$ft_home = (int)$this->home_score;
			$ft_away = (int)$this->away_score;
			$this->calculateHTFTDouble($ht_home,$ht_away,$ft_home,$ft_away);
		}
		
		// 1x2
		if($each_bet->market_name == "1x2"){
            $this->setScore();
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateMatchWinner($home,$away);
		}
	}
}
