<?php

namespace App\Http\Controllers\BetCalculations;

use Illuminate\Http\Request;
use App\Betslip;
use App\FeedModel\Match;
/**
 * This will control the calculation of all markets for Volleyball
 *
 * @author Arijit Jana
 */
class VolleyballCalculation extends BetCalculation
{
	/**
	 * name of all the markets which can be calculated if first set is over
	 * @var array
	 */
	public $first_set_odds 		= ['Home/Away (1st Set)', 'Total Points Odd/Even (1st Set)', 'Asian Handicap (1st Set)'];

	/**
	 * name of all the markets which can be calculated if second set is over
	 * @var array
	 */
	public $second_set_odds 	= ['Home/Away (2nd Set)', 'Odd/Even (2nd Set)', 'Asian Handicap (2nd Set)'];

	/**
	 * name of all the markets which can be calculated if third set is over
	 * @var array
	 */
	public $third_set_odds 		= ['Home/Away (3rd Set)', 'Asian Handicap (3rd Set)', 'Total Points Odd/Even (3rd Set)'];
	
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
	 * home team won set for a particular match
	 * @var integer
	 */
	public $home_set;

	/**
	 * away team won set for a particular match
	 * @var integer
	 */
    public $away_set;
	
	/**
	 * calculates all single bets for Volleyball
	 * @return string     returns the time of the calculation
	 */
    public function volleyballSingleBetCalculate(){
		$this->singleBetCalculation('Volleyball');
	}
	
	/**
	 * calculates all combo bets for Volleyball
	 * @return string     returns the time of the calculation
	 */
	public function volleyballComboBetCalculate(){
		$this->comboBetCalculation('Volleyball');
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
		if(in_array($market_name,$this->first_set_odds)){
			$this->odds_set = 1;
		}
		else if(in_array($market_name,$this->second_set_odds)){
			$this->odds_set = 2;
		}
		else if(in_array($market_name,$this->third_set_odds)){
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
	public function setScore($half = null){
		$scores = explode(",",$this->match_info->score);
		(int)$home_score = 0;
		(int)$away_score = 0;
		if($half == '1st'){
			$score_array = explode(':', $scores[0]);
			$home_score = $score_array[0];
			$away_score = $score_array[1];
		}
		else if($half == '2nd'){
			$score_array = explode(':', $scores[1]);
			$home_score = $score_array[0];
			$away_score = $score_array[1];
		}
		else if($half == '3rd'){
			$score_array = explode(':', $scores[2]);
			$home_score = $score_array[0];
			$away_score = $score_array[1];
		}
		else if($half == '4th'){
			if(isset($scores[3])){
				$score_array = explode(':', $scores[3]);
				$home_score = $score_array[0];
				$away_score = $score_array[1];
			}
		}
		else if($half == '5th'){
			if(isset($scores[4])){
				$score_array = explode(':', $scores[4]);
				$home_score = $score_array[0];
				$away_score = $score_array[1];
			}
		}
		else if($half == '6th'){
			if(isset($scores[5])){
				$score_array = explode(':', $scores[5]);
				$home_score = $score_array[0];
				$away_score = $score_array[1];
			}
		}
		else if($half == '7th'){
			if(isset($scores[6])){
				$score_array = explode(':', $scores[6]);
				$home_score = $score_array[0];
				$away_score = $score_array[1];
			}
		}
		else{
			foreach($scores as $score) {
				$score_array = explode(':', $score);
				$home_score = (int)$home_score + $score_array[0];;
				$away_score = (int)$away_score + $score_array[1];
			}
		}
		$this->home_score = (int)$home_score;
		$this->away_score = (int)$away_score;
    }
	
	/**
	 * this will set the sets won by home or away for a match
	 */
	public function setWonSets() {
		$scores = explode(",",$this->match_info->score);
		(int)$home_set = 0;
		(int)$away_set = 0;
		foreach($scores as $score) {
			$score_array = explode(':', $score);
			if($score_array[0] > $score_array[1]){
				$home_set++;
			}
			if($score_array[0] < $score_array[1]){
				$away_set++;
			}
		}
		$this->home_set = (int)$home_set;
		$this->away_set = (int)$away_set;
	}
	
	/**
	 * this will calculate each market
	 */
	public function CalculateEachBet() {
		$each_bet = $this->single_bet_info;
		
		// Home/Away
		if($each_bet->market_name == "Home/Away"){
			$this->setWonSets();
			$home = (int)$this->home_set;
			$away = (int)$this->away_set;
			$this->calculateHomeAway($home,$away);
		}
		
		// Home/Away (1st Set)
		if($each_bet->market_name == "Home/Away (1st Set)"){
			$this->setScore('1st');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateHomeAway($home,$away);
		}
		
		// Home/Away (2nd Set)
		if($each_bet->market_name == "Home/Away (2nd Set)"){
			$this->setScore('2nd');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateHomeAway($home,$away);
		}
		
		// Home/Away (3rd Set)
		if($each_bet->market_name == "Home/Away (3rd Set)"){
			$this->setScore('3rd');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateHomeAway($home,$away);
		}
		
		// Home/Away (6th Set)
		if($each_bet->market_name == "Home/Away (6th Set)"){
			$this->setScore('6th');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateHomeAway($home,$away);
		}
		
		// Home/Away (7th Set)
		if($each_bet->market_name == "Home/Away (7th Set)"){
			$this->setScore('7th');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateHomeAway($home,$away);
		}
		
		// Asian Handicap (Points)
		if($each_bet->market_name == "Asian Handicap (Points)"){
			$this->setScore();
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateAsianHandicap($home,$away);
		}
		
		// Asian Handicap (Sets)
		if($each_bet->market_name == "Asian Handicap (Sets)"){
			$this->setWonSets();
			$home = (int)$this->home_set;
			$away = (int)$this->away_set;
			$this->calculateAsianHandicap($home,$away);
		}
		
		// Asian Handicap (1st Set)
		if($each_bet->market_name == "Asian Handicap (1st Set)"){
			$this->setScore('1st');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateAsianHandicap($home,$away);
		}
		
		// Asian Handicap (2nd Set)
		if($each_bet->market_name == "Asian Handicap (2nd Set)"){
			$this->setScore('2nd');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateAsianHandicap($home,$away);
		}
		
		// Asian Handicap (3rd Set)
		if($each_bet->market_name == "Asian Handicap (3rd Set)"){
			$this->setScore('3rd');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateAsianHandicap($home,$away);
		}
		
		// Asian Handicap (6th Set)
		if($each_bet->market_name == "Asian Handicap (6th Set)"){
			$this->setScore('6th');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateAsianHandicap($home,$away);
		}
		
		// Asian Handicap (7th Set)
		if($each_bet->market_name == "Asian Handicap (7th Set)"){
			$this->setScore('7th');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateAsianHandicap($home,$away);
		}
		
		// Total Points Odd/Even
		if($each_bet->market_name == "Total Points Odd/Even"){
			$this->setScore();
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateOddEven($home,$away);
		}
		
		// Total Points Odd/Even (1st Set)
		if($each_bet->market_name == "Total Points Odd/Even (1st Set)"){
			$this->setScore('1st');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateOddEven($home,$away);
		}
		
		// Odd/Even (2nd Set)
		if($each_bet->market_name == "Odd/Even (2nd Set)"){
			$this->setScore('2nd');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateOddEven($home,$away);
		}
		
		// Total Points Odd/Even (3rd Set)
		if($each_bet->market_name == "Total Points Odd/Even (3rd Set)"){
			$this->setScore('2nd');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateOddEven($home,$away);
		}
		
		// First Set/Match
		if($each_bet->market_name == "First Set/Match"){
			$this->setScore('1st');
			$home_fs = (int)$this->home_score;
			$away_fs = (int)$this->away_score;
			$this->setWonSets();
			$home = (int)$this->home_set;
			$away = (int)$this->away_set;
			$this->calculateFirstSetAndMatch($home_fs,$away_fs,$home,$away);
		}
		
		// Set Betting
		if($each_bet->market_name == "Set Betting"){
			$this->setWonSets();
			$home = (int)$this->home_set;
			$away = (int)$this->away_set;
			$this->calculateCorrectScore($home,$away);
		}
		
		// Correct Number of Sets
		if($each_bet->market_name == "Correct Number of Sets"){
			$this->setWonSets();
			$home = (int)$this->home_set;
			$away = (int)$this->away_set;
			$this->calculateCorrectNumberOfSets($home,$away);
		}
	}
}
