<?php

namespace App\Http\Controllers\BetCalculations;

use Illuminate\Http\Request;
use App\Betslip;
use App\FeedModel\Match;
/**
 * This will control the calculation of all markets for American Football
 *
 * @author Arijit Jana
 */
class AmericanFootballCalculation extends BetCalculation
{
	/**
	 * name of all the markets which can only be calculated after match is finished
	 * @var array
	 */
	public $fulltime_odds = ['Home/Away', 'Home/Away (Including OT)', 'Over/Under', 'Asian Handicap', 'Asian Handicap (Including OT)', 'Odd/Even', 'Half Time/Full Time', '1x2', 'Highest Scoring Half', 'Highest Scoring Quarter', 'Overtime'];

	/**
	 * name of all the markets which can be calculated if first quarter is over
	 * @var array
	 */
	public $first_qtr_odds = ['Home/Away (1st Quarter)', 'Asian Handicap (1st Quarter)'];

	/**
	 * name of all the markets which can be calculated if second quarter is over
	 * @var array
	 */
	public $second_qtr_odds = ['Home/Away (1st Half)', 'Home/Away (2nd Quarter)', 'Asian Handicap (1st Half)', 'Asian Handicap (2nd Quarter)'];

	/**
	 * name of all the markets which can be calculated if thrid quarter is over
	 * @var array
	 */
	public $third_qtr_odds = ['Home/Away (3rd Quarter)', 'Asian Handicap (3rd Quarter)'];

	/**
	 * name of all the markets which can be calculated if fourth quarter is over
	 * @var array
	 */
	public $fourth_qtr_odds = ['Home/Away (2nd Half)', 'Home/Away (4th Quarter)', 'Asian Handicap (2nd Half)', 'Asian Handicap (4th Quarter)'];
	
	/**
	 * period value that indicates the match is over
	 * @var array
	 */
	public $fulltime_match_status   	= ['255'];
	
	/**
	 * period values that indicates the match haven't reached the half time yet
	 * @var array
	 */
	public $pre_halftime_match_status   = ['0', '-1', '1', '2'];

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
	 * calculates all single bets for American Football
	 * @return string     returns the time of the calculation
	 */
    public function americanfootballSingleBetCalculate(){
		$this->singleBetCalculation('American Football');
	}

	/**
	 * calculates all combo bets for American Football
	 * @return string     returns the time of the calculation
	 */
	public function americanfootballComboBetCalculate(){
		$this->comboBetCalculation('American Football');
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
			else if($this->match_info->period == "-1"){
				$this->calculate_bet = false;
			}
			else if($this->match_info->period == "5"){
				if((int)$this->odds_set <= 4){
					$this->calculate_bet = true;
				}
				else{
					$this->calculate_bet = false;
				}
			}
			else{
				// live match
				if($this->match_info->period > 1 && $this->odds_set == 1) {
					$this->calculate_bet = true;
				}
				else if($this->match_info->period > 2 && $this->odds_set == 2) {
					$this->calculate_bet = true;
				}
				else if($this->match_info->period > 3 && $this->odds_set == 3) {
					$this->calculate_bet = true;
				}
				else if($this->match_info->period > 4 && $this->odds_set == 4) {
					$this->calculate_bet = true;
				}
				else{
						$this->calculate_bet = false;
				}
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
		$match_id = $this->single_bet_info->match_id;
		// $odds_type = $this->single_bet_info->odds_type;
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
			$this->no_data_found = true;
			return false;
		}
	}
	
	/**
	 * this will set the odds set number
	 * @return integer     odds set number indicates in which period of the match does the market belongs
	 */
	public function getOddsSetNumber(){
		$market_name = $this->single_bet_info->market_name;
		if(in_array($market_name,$this->first_qtr_odds)){
			$this->odds_set = 1;
		}
		else if(in_array($market_name,$this->second_qtr_odds)){
			$this->odds_set = 2;
		}
		else if(in_array($market_name,$this->third_qtr_odds)){
			$this->odds_set = 3;
		}
		else if(in_array($market_name,$this->fourth_qtr_odds)){
			$this->odds_set = 4;
		}
		else{
			$this->odds_set = 5;
		}
	}
	
	/**
	 * this will set score of home or away for a match for the given period
	 * @param string 	$period 	period of the match of whos result is needed
	 */
	public function setScore($period = null){
		$scores = explode(" ",$this->match_info->score);
		$all_set_scores = str_replace(")","",str_replace("(","",$scores[1]));
		$each_set_scores = explode(',', $all_set_scores);
        if($period == "st"){
            // need to set only second half score
			$third_qtr_score = explode(':', $each_set_scores[2]);
			$fourth_qtr_score = explode(':', $each_set_scores[3]);
			$home_score = $third_qtr_score[0] + $fourth_qtr_score[0];
			$away_score = $third_qtr_score[1] + $fourth_qtr_score[1];
        }
		elseif($period == "ht"){
			// only half time
			$first_qtr_score = explode(':', $each_set_scores[0]);
			$second_qtr_score = explode(':', $each_set_scores[1]);
			$home_score = $first_qtr_score[0] + $second_qtr_score[0];
			$away_score = $first_qtr_score[1] + $second_qtr_score[1];
		}
		elseif($period == "1st"){
			// only 1st Quarter time
			$first_qtr_score = explode(':', $each_set_scores[0]);
			$home_score = $first_qtr_score[0];
			$away_score = $first_qtr_score[1];
		}
		elseif($period == "2nd"){
			// only 2nd Quarter time
			$second_qtr_score = explode(':', $each_set_scores[1]);
			$home_score = $second_qtr_score[0];
			$away_score = $second_qtr_score[1];
		}
		elseif($period == "3rd"){
			// only 3rd Quarter time
			$third_qtr_score = explode(':', $each_set_scores[2]);
			$home_score = $third_qtr_score[0];
			$away_score = $third_qtr_score[1];
		}
		elseif($period == "4th"){
			// only 4th Quarter time
			$fourth_qtr_score = explode(':', $each_set_scores[3]);
			$home_score = $fourth_qtr_score[0];
			$away_score = $fourth_qtr_score[1];
		}
		else{
			// full time score including over time
			$score_array = explode(':', $scores[0]);
			$home_score = $score_array[0];
			$away_score = $score_array[1];
			if($period != "ot"){
				// full time score excluding over time
				if(isset($each_set_scores[4])){
					$over_time_score = explode(':', $each_set_scores[4]);
					$home_score = $home_score - $over_time_score[0];
					$away_score = $away_score - $over_time_score[1];
				}
			}
		}
		$this->home_score = (int)$home_score;
		$this->away_score = (int)$away_score;
    }
	
	/**
	 * this will calculate each market
	 */
	public function CalculateEachBet() {
		$each_bet = $this->single_bet_info;
		
		// Home/Away
		if($each_bet->market_name == "Home/Away"){
			$this->setScore();
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateHomeAway($home,$away);
		}
		
		// Home/Away (Including OT)
		if($each_bet->market_name == "Home/Away (Including OT)"){
			$this->setScore('ot');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateHomeAway($home,$away);
		}
		
		// Home/Away (1st Half)
		if($each_bet->market_name == "Home/Away (1st Half)"){
			$this->setScore('ht');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateHomeAway($home,$away);
		}
		
		// Home/Away (2nd Half)
		if($each_bet->market_name == "Home/Away (2nd Half)"){
			$this->setScore('st');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateHomeAway($home,$away);
		}
		
		// Home/Away (1st Quarter)
		if($each_bet->market_name == "Home/Away (1st Quarter)"){
			$this->setScore('1st');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateHomeAway($home,$away);
		}
		
		// Home/Away (2nd Quarter)
		if($each_bet->market_name == "Home/Away (2nd Quarter)"){
			$this->setScore('2nd');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateHomeAway($home,$away);
		}
		
		// Home/Away (3rd Quarter)
		if($each_bet->market_name == "Home/Away (3rd Quarter)"){
			$this->setScore('3rd');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateHomeAway($home,$away);
		}
		
		// Home/Away (4th Quarter)
		if($each_bet->market_name == "Home/Away (4th Quarter)"){
			$this->setScore('4th');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateHomeAway($home,$away);
		}
		
		// Over/Under
		if($each_bet->market_name == "Over/Under"){
			$this->setScore();
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateOverUnder($home,$away);
		}
		
		// Asian Handicap
		if($each_bet->market_name == "Asian Handicap"){
			$this->setScore();
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateAsianHandicap($home,$away);
		}
		
		// Asian Handicap (Including OT)
		if($each_bet->market_name == "Asian Handicap (Including OT)"){
			$this->setScore('ot');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateAsianHandicap($home,$away);
		}
		
		// Asian Handicap (1st Half)
		if($each_bet->market_name == "Asian Handicap (1st Half)"){
			$this->setScore('ht');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateAsianHandicap($home,$away);
		}
		
		// Asian Handicap (2nd Half)
		if($each_bet->market_name == "Asian Handicap (2nd Half)"){
			$this->setScore('st');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateAsianHandicap($home,$away);
		}
		
		// Asian Handicap (1st Quarter)
		if($each_bet->market_name == "Asian Handicap (1st Quarter)"){
			$this->setScore('1st');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateAsianHandicap($home,$away);
		}
		
		// Asian Handicap (2nd Quarter)
		if($each_bet->market_name == "Asian Handicap (2nd Quarter)"){
			$this->setScore('2nd');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateAsianHandicap($home,$away);
		}
		
		// Asian Handicap (3rd Quarter)
		if($each_bet->market_name == "Asian Handicap (3rd Quarter)"){
			$this->setScore('3rd');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateAsianHandicap($home,$away);
		}
		
		// Asian Handicap (4th Quarter)
		if($each_bet->market_name == "Asian Handicap (4th Quarter)"){
			$this->setScore('4th');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateAsianHandicap($home,$away);
		}
		
		// Odd/Even
		if($each_bet->market_name == "Odd/Even"){
			$this->setScore();
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateOddEven($home,$away);
		}
		
		// Half Time/Full Time
		if($each_bet->market_name == "Half Time/Full Time"){
			$this->setScore('ht');
			$home_ht = (int)$this->home_score;
			$away_ht = (int)$this->away_score;
			$this->setScore();
			$home_ft = (int)$this->home_score;
			$away_ft = (int)$this->away_score;
			$this->calculateHTFT($home_ht,$away_ht,$home_ft,$away_ft);
		}
		
		// 1x2
		if($each_bet->market_name == "1x2"){
			$this->setScore();
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateMatchWinner($home,$away);
		}
		
		// Highest Scoring Half
		if($each_bet->market_name == "Highest Scoring Half"){
			$this->setScore('ht');
			$first_half = (int)$this->home_score + (int)$this->away_score;
			$this->setScore('st');
			$second_half = (int)$this->home_score + (int)$this->away_score;
			$this->calculateHighestScoringHalf($first,$second);
		}
		
		// Highest Scoring Quarter
		if($each_bet->market_name == "Highest Scoring Quarter"){
			$total_score					= array();
			$this->setScore('1st');
			$total_score['1st Quarter'] 	= (int)$this->home_score + (int)$this->away_score;
			$this->setScore('2nd');
			$total_score['2nd Quarter'] 	= (int)$this->home_score + (int)$this->away_score;
			$this->setScore('3rd');
			$total_score['3rd Quarter'] 	= (int)$this->home_score + (int)$this->away_score;
			$this->setScore('4th');
			$total_score['4th Quarter'] 	= (int)$this->home_score + (int)$this->away_score;
			$this->calculateHighestScoringQuarter($total_score);
		}
		
		// Overtime
		if($each_bet->market_name == "Overtime"){
			$this->setScore('ot');
			$home_ft = (int)$this->home_score;
			$away_ft = (int)$this->away_score;
			$this->setScore();
			$home_qtr = (int)$this->home_score;
			$away_qtr = (int)$this->away_score;
			$this->calculateOverTime($home_qtr,$away_qtr,$home_ft,$away_ft);
		}
	}
}
