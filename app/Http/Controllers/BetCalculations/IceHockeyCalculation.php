<?php

namespace App\Http\Controllers\BetCalculations;

use Illuminate\Http\Request;
use App\Betslip;
use App\FeedModel\Match;
/**
 * This will control the calculation of all markets for Ice Hockey
 *
 * @author Arijit Jana
 */
class IceHockeyCalculation extends BetCalculation
{
	/**
	 * name of all the markets which can be calculated if first period is over
	 * @var array
	 */
	public $first_period_odds 		= ['Home/Away (1st Period)', 'Asian Handicap (1st Period)', 'Odd/Even (1st Period)', 'Both Teams To Score (1st Period)', 'Correct Score (1st Period)'];

	/**
	 * name of all the markets which can be calculated if second period is over
	 * @var array
	 */
	public $second_period_odds 		= ['Home/Away (2nd Period)', 'Asian Handicap (2nd Period)', 'Odd/Even (2nd Period)', 'Both Teams To Score (2nd Period)', 'Correct Score (2nd Period)'];

	/**
	 * name of all the markets which can be calculated if third period is over
	 * @var array
	 */
	public $third_period_odds 		= ['Home/Away (3rd Period)', 'Asian Handicap (3rd Period)', 'Odd/Even (3rd Period)', 'Both Teams To Score (3rd Period)', 'Correct Score (3rd Period)'];

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
	 * tells if a match went to over time or not
	 * @var boolean
	 */
    public $overtime;
	
	/**
	 * calculates all single bets for Ice Hockey
	 * @return string     returns the time of the calculation
	 */
    public function icehockeySingleBetCalculate(){
		$this->singleBetCalculation('Ice Hockey');
	}
	
	/**
	 * calculates all combo bets for Ice Hockey
	 * @return string     returns the time of the calculation
	 */
	public function icehockeyComboBetCalculate(){
		$this->comboBetCalculation('Ice Hockey');
	}
	
	/**
	 * this will check match status to calculate bet
	 * @return boolean     true = do calculate / false = don't calculate
	 */
	public function checkMatchStatus(){
		// now we should check match status
		if($this->checkBetCancelStatus() == true && $this->match_info != false && !in_array($this->match_info->period,$this->cancelled_match_status)){
			if(in_array($this->match_info->period,$this->fulltime_match_status)){
				$this->calculate_bet = true;
			}
			else if($this->match_info->period == "-1"){
				$this->calculate_bet = false;
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
			$this->match_info = $match_details;
			$this->getOddsSetNumber();
			$this->no_data_found = false;
			if($this->match_info->status == "active"){
				return true;
			}
			else if($this->match_info->status == "cancel"){
				return false;
			}
			else{
				// full time odds and cancelled
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
		if(in_array($market_name,$this->first_period_odds)){
			$this->odds_set = 1;
		}
		else if(in_array($market_name,$this->second_period_odds)){
			$this->odds_set = 2;
		}
		else if(in_array($market_name,$this->third_period_odds)){
			$this->odds_set = 3;
		}
		else{
			// full time odds and cancelled after 3rd set_error_handler
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
		if($period == "1st"){
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
		else{
			// full time score including over time
			$score_array = explode(':', $scores[0]);
			$home_score = $score_array[0];
			$away_score = $score_array[1];
			if($period != "ot"){
				// full time score excluding over time
				if(isset($scores[2])){
					$over_time_array = explode('.', $scores[2]);
					$over_time_score = str_replace(")","",str_replace("(","",$over_time_array[1]));
					$over_time_score_array = explode(':', $over_time_score);
					$home_score = $home_score - $over_time_score_array[0];
					$away_score = $away_score - $over_time_score_array[1];
				}
			}
		}
		$this->home_score = (int)$home_score;
		$this->away_score = (int)$away_score;
    }
	
	/**
	 * checkes if a match went to over time or not
	 * @return boolean     true = match went to over time / false = not over time
	 */
	public function checkForOvertime() {
		$scores = explode(" ",$this->match_info->score);
		if(isset($scores[2])){
			$this->overtime = true;
		}
		else{
			$this->overtime = false;
		}
	}
	
	/**
	 * this will calculate each market
	 */
	public function CalculateEachBet() {
		$each_bet = $this->single_bet_info;
		
		// 1x2
		if($each_bet->market_name == "1x2"){
            $this->setScore();
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateMatchWinner($home,$away);
		}
		
		// Home/Away
		if($each_bet->market_name == "Home/Away"){
            $this->setScore();
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateHomeAway($home,$away);
		}
		
		// Home/Away (1st Period)
		if($each_bet->market_name == "Home/Away (1st Period)"){
            $this->setScore('1st');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateHomeAway($home,$away);
		}
		
		// Home/Away (2nd Period)
		if($each_bet->market_name == "Home/Away (2nd Period)"){
            $this->setScore('2nd');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateHomeAway($home,$away);
		}
		
		// Home/Away (3rd Period)
		if($each_bet->market_name == "Home/Away (3rd Period)"){
            $this->setScore('3rd');
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
		
		// Asian Handicap
		if($each_bet->market_name == "Asian Handicap"){
            $this->setScore();
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateAsianHandicap($home,$away);
		}
		
		// Asian Handicap (1st Period)
		if($each_bet->market_name == "Asian Handicap (1st Period)"){
            $this->setScore('1st');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateAsianHandicap($home,$away);
		}
		
		// Asian Handicap (2nd Period)
		if($each_bet->market_name == "Asian Handicap (2nd Period)"){
            $this->setScore('2nd');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateAsianHandicap($home,$away);
		}
		
		// Asian Handicap (3rd Period)
		if($each_bet->market_name == "Asian Handicap (3rd Period)"){
            $this->setScore('2nd');
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
		
		// Over/Under
		if($each_bet->market_name == "Over/Under"){
            $this->setScore();
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateOverUnder($home,$away);
		}
		
		// Odd/Even
		if($each_bet->market_name == "Odd/Even"){
            $this->setScore();
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateOddEven($home,$away);
		}
		
		// Odd/Even (1st Period)
		if($each_bet->market_name == "Odd/Even (1st Period)"){
            $this->setScore('1st');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateOddEven($home,$away);
		}
		
		// Odd/Even (2nd Period)
		if($each_bet->market_name == "Odd/Even (2nd Period)"){
            $this->setScore('2nd');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateOddEven($home,$away);
		}
		
		// Odd/Even (3rd Period)
		if($each_bet->market_name == "Odd/Even (3rd Period)"){
            $this->setScore('3rd');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateOddEven($home,$away);
		}
		
		// Odd/Even (Including OT)
		if($each_bet->market_name == "Odd/Even (Including OT)"){
            $this->setScore('ot');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateOddEven($home,$away);
		}
		
		// Double Chance
		if($each_bet->market_name == "Double Chance"){
            $this->setScore();
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateDoubleChance($home,$away);
		}
		
		// Correct Score
		if($each_bet->market_name == "Correct Score"){
            $this->setScore();
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateCorrectScore($home,$away);
		}
		
		// Correct Score (1st Period)
		if($each_bet->market_name == "Correct Score (1st Period)"){
            $this->setScore('1st');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateCorrectScore($home,$away);
		}
		
		// Correct Score (2nd Period)
		if($each_bet->market_name == "Correct Score (2nd Period)"){
            $this->setScore('2nd');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateCorrectScore($home,$away);
		}
		
		// Correct Score (3rd Period)
		if($each_bet->market_name == "Correct Score (3rd Period)"){
            $this->setScore('3rd');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateCorrectScore($home,$away);
		}
		
		// Will match go to overtime?
		if($each_bet->market_name == "Will match go to overtime?"){
            $this->checkForOvertime();
			$overtime = $this->overtime;
			$this->calculateWillGoToOvertime($overtime);
		}
		
		// Both Teams To Score
		if($each_bet->market_name == "Both Teams To Score"){
            $this->setScore();
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateBothTeamToScore($home,$away);
		}
		
		// Both Teams To Score (1st Period)
		if($each_bet->market_name == "Both Teams To Score (1st Period)"){
            $this->setScore('1st');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateBothTeamToScore($home,$away);
		}
		
		// Both Teams To Score (2nd Period)
		if($each_bet->market_name == "Both Teams To Score (2nd Period)"){
            $this->setScore('2nd');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateBothTeamToScore($home,$away);
		}
		
		// Both Teams To Score (3rd Period)
		if($each_bet->market_name == "Both Teams To Score (3rd Period)"){
            $this->setScore('2nd');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateBothTeamToScore($home,$away);
		}
		
		// Highest Scoring Period
		if($each_bet->market_name == "Highest Scoring Period"){
            $this->setScore('1st');
			$fp_home = (int)$this->home_score;
			$fp_away = (int)$this->away_score;
			$this->setScore('2nd');
			$sp_home = (int)$this->home_score;
			$sp_away = (int)$this->away_score;
			$this->setScore('3rd');
			$tp_home = (int)$this->home_score;
			$tp_away = (int)$this->away_score;
			$this->calculateHighestScoringPeriod($fp_home,$fp_away,$sp_home,$sp_away,$tp_home,$tp_away);
		}
		
		// Result/Total Goals
		if($each_bet->market_name == "Result/Total Goals"){
            $this->setScore();
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateResultTotalGoal($home,$away);
		}
	}
}
