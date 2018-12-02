<?php

namespace App\Http\Controllers\BetCalculations;

use Illuminate\Http\Request;
use App\Betslip;
use App\FeedModel\Match;
/**
 * This will control the calculation of all markets for Baseball
 *
 * @author Arijit Jana
 */
class BaseballCalculation extends BetCalculation
{
	/**
	 * name of all the markets which can be calculated if first innings is over
	 * @var array
	 */
	public $first_innings_odds 		= ['Home/Away (1st Inning)', 'A Run (1st Inning)', 'Asian Handicap (1st Inning)'];

	/**
	 * name of all the markets which can be calculated if thrid innings is over
	 * @var array
	 */
	public $third_innings_odds 		= ['Asian Handicap (1st 3 Innings)'];
	
	/**
	 * name of all the markets which can be calculated if fifth innings is over
	 * @var array
	 */
	public $fifth_innings_odds 		= ['Asian Handicap (1st 5 Innings)'];
	
	/**
	 * name of all the markets which can be calculated if seventh innings is over
	 * @var array
	 */
	public $seventh_innings_odds 	= ['Asian Handicap (1st 7 Innings)'];
	
	/**
	 * period value that indicates the match is over
	 * @var array
	 */
    public $fulltime_match_status   	= ['255'];

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
	 * calculates all single bets for Baseball
	 * @return string     returns the time of the calculation
	 */
    public function baseballSingleBetCalculate(){
		$this->singleBetCalculation('Baseball');
	}
	
	/**
	 * calculates all combo bets for Baseball
	 * @return string     returns the time of the calculation
	 */
	public function baseballComboBetCalculate(){
		$this->comboBetCalculation('Baseball');
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
				if($this->match_info->period > $this->odds_set) {
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
		if(in_array($market_name,$this->first_innings_odds)){
			$this->odds_set = 1;
		}
		else if(in_array($market_name,$this->third_innings_odds)){
			$this->odds_set = 3;
		}
		else if(in_array($market_name,$this->fifth_innings_odds)){
			$this->odds_set = 5;
		}
		else if(in_array($market_name,$this->seventh_innings_odds)){
			$this->odds_set = 7;
		}
		else{
			// full time odds and cancelled after 3rd set_error_handler
			$this->odds_set = 15;
		}
	}
	
	/**
	 * this will set score of home or away for a match for the given period
	 * @param string 	$period 	period of the match of whos result is needed
	 */
	public function setScore($period = null){
		$match_result = explode(" ",$this->match_info->score);
		$scores = trim(trim(trim($match_result[0], "R"), "("), ")");
		$full_time = explode(':', $scores);
		$all_inn = trim(trim(trim($match_result[1], "R"), "("), ")");
		$inn_array = explode(',', $all_inn);
		(int)$home_score = 0;
		(int)$away_score = 0;
		if ($period == 'ot') {
			$home_score = (int)$full_time[0];
			$away_score = (int)$full_time[1];
		}
		elseif(is_numeric($period)){
			foreach($inn_array as $k => $score) {
				if($k < $period) {
					$score_array = explode(':', $score);
					$home_score = (int)$home_score + $score_array[0];
					$away_score = (int)$away_score + $score_array[1];
				}
				else{
					break;
				}
			}
		}
		else{
			foreach($inn_array as $i => $score) {
				if($i < 9) {
					$score_array = explode(':', $score);
					$home_score = (int)$home_score + $score_array[0];
					$away_score = (int)$away_score + $score_array[1];
				}
				else{
					break;
				}
			}
		}
		$this->home_score = (int)$home_score;
		$this->away_score = (int)$away_score;
    }
	
	/**
	 * checks if match went to extra innings
	 * @return boolean     true/false
	 */
	public function isExtraInnings(){
		$scores = explode(",",$this->match_info->score);
		if(isset($scores[9])) {
			return true;
		}
		else{
			return false;
		}
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
		
		// Home/Away (1st Inning)
		if($each_bet->market_name == "Home/Away (1st Inning)"){
			$this->setScore(1);
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
		
		// Asian Handicap (1st 5 Innings)
		if($each_bet->market_name == "Asian Handicap (1st 5 Innings)"){
			$this->setScore(5);
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateAsianHandicap($home,$away);
		}
		
		// Asian Handicap (1st 3 Innings)
		if($each_bet->market_name == "Asian Handicap (1st 3 Innings)"){
			$this->setScore(3);
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateAsianHandicap($home,$away);
		}
		
		// Asian Handicap (1st 7 Innings)
		if($each_bet->market_name == "Asian Handicap (1st 7 Innings)"){
			$this->setScore(7);
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateAsianHandicap($home,$away);
		}
		
		// Asian Handicap (1st Inning)
		if($each_bet->market_name == "Asian Handicap (1st Inning)"){
			$this->setScore(1);
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
		
		// Odd/Even (Including OT)
		if($each_bet->market_name == "Odd/Even (Including OT)"){
			$this->setScore('ot');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateOddEven($home,$away);
		}
		
		// 1x2
		if($each_bet->market_name == "1x2"){
			$this->setScore();
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateMatchWinner($home,$away);
		}
		
		// A Run (1st Inning)
		if($each_bet->market_name == "A Run (1st Inning)"){
			$this->setScore(1);
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateScoreARun($home,$away);
		}
		
		// A Run (1st Inning)
		if($each_bet->market_name == "A Run (1st Inning)"){
			$this->setScore(1);
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateScoreARun($home,$away);
		}
		
		// Team with Highest Scoring Innings
		if($each_bet->market_name == "Team with Highest Scoring Innings"){
			$all_scores_home = [];
			$all_scores_away = [];
			$all_scores = [];
			for($i = 1; $i <=9; $i++){
				$this->setScore($i);
				$home = (int)$this->home_score;
				$away = (int)$this->away_score;
				$all_scores[] = (int)$this->home_score;
				$all_scores[] = (int)$this->away_score;
				$all_scores_home[$i.'_Home'] = (int)$this->home_score;
				$all_scores_away[$i.'_Away'] = (int)$this->away_score;
			}
			$highest_score = max($all_scores);
			$this->calculateHighestSocrerTeam($all_scores_home,$all_scores_away,$highest_score);
		}
		
		// Extra Innings
		if($each_bet->odds_type == "Extra Innings"){
			$exrta_innings = $this->isExtraInnings();
			$this->calculateIfExtraInnings($exrta_innings);
		}
	}
}
