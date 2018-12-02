<?php

namespace App\Http\Controllers\BetCalculations;

use Illuminate\Http\Request;
use App\Betslip;
use App\FeedModel\Match;
/**
 * This will control the calculation of all markets for Tennis
 *
 * @author Arijit Jana
 */
class TennisCalculation extends BetCalculation
{
	/**
	 * name of all the markets which can be calculated if first set is over
	 * @var array
	 */
	public $first_set_odds 				= ['Home/Away (1st Set)', 'Asian Handicap by Games (1st Set)', 'Tie-break (1st Set)', 'Odd/Even (1st Set)', 'Correct Score (1st Set)', 'How many games will be played in 1st set?'];

	/**
	 * name of all the markets which can be calculated if second quarter is over
	 * @var array
	 */
	public $second_set_odds 			= ['Home/Away (2nd Set)', 'Asian Handicap by Games (2nd Set)', 'Odd/Even (2nd Set)', 'Correct Score (2nd Set)', 'How many games will be played in 2nd set?'];

	/**
	 * name of all the markets which can be calculated if third quarter is over
	 * @var array
	 */
	public $third_set_odds 				= ['Home/Away (3rd Set)', 'Tie-break (3rd Set)', 'Correct Score (3rd Set)', 'How many games will be played in 3rd set?'];
	
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
	 * calculates all single bets for Tennis
	 * @return string     returns the time of the calculation
	 */
    public function tennisSingleBetCalculate(){
		$this->singleBetCalculation('Tennis');
	}
	
	/**
	 * calculates all combo bets for Tennis
	 * @return string     returns the time of the calculation
	 */
	public function tennisComboBetCalculate(){
		$this->comboBetCalculation('Tennis');
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
				// full time odds and cancelled after 4 qtr
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
			$home_score = explode('(', $score_array[0])[0];
			$away_score = explode('(', $score_array[1])[0];
		}
		else if($half == '2nd'){
			$score_array = explode(':', $scores[1]);
			$home_score = explode('(', $score_array[0])[0];
			$away_score = explode('(', $score_array[1])[0];
		}
		else if($half == '3rd'){
			if(isset($scores[2])){
				$score_array = explode(':', $scores[2]);
				$home_score = explode('(', $score_array[0])[0];
				$away_score = explode('(', $score_array[1])[0];
			}
		}
		else if($half == '4th'){
			if(isset($scores[3])){
				$score_array = explode(':', $scores[3]);
				$home_score = explode('(', $score_array[0])[0];
				$away_score = explode('(', $score_array[1])[0];
			}
		}
		else if($half == '5th'){
			if(isset($scores[4])){
				$score_array = explode(':', $scores[4]);
				$home_score = explode('(', $score_array[0])[0];
				$away_score = explode('(', $score_array[1])[0];
			}
		}
		else{
			foreach($scores as $score) {
				$score_array = explode(':', $score);
				$home_score_array = explode('(', $score_array[0]);
				$away_score_array = explode('(', $score_array[1]);
				$home_score = (int)$home_score + $home_score_array[0];
				$away_score = (int)$away_score + $away_score_array[0];
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
		(int)$home_score = 0;
		(int)$away_score = 0;
		foreach($scores as $score) {
			$score_array = explode(':', $score);
			$home_score_array = explode('(', $score_array[0]);
			$away_score_array = explode('(', $score_array[1]);
			if($home_score_array[0] > $away_score_array[0]){
				$home_score++;
			}
			if($home_score_array[0] < $away_score_array[0]){
				$away_score++;
			}
		}
		$this->home_set = (int)$home_score;
		$this->away_set = (int)$away_score;
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
		
		// Home/Away (Game)
		if($each_bet->market_name == "Home/Away (Game)"){
			$this->setScore();
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
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
		
		// Tie-break
		if($each_bet->market_name == "Tie-break"){
			$this->setWonSets();
			$home = (int)$this->home_set;
			$away = (int)$this->away_set;
			$this->calculateTieBreak($home,$away,2);
		}
		
		// Tie-break (1st Set)
		if($each_bet->market_name == "Tie-break (1st Set)"){
			$this->setScore('1st');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateTieBreak($home,$away,11);
		}
		
		// Tie-break (2nd Set)
		if($each_bet->market_name == "Tie-break (2nd Set)"){
			$this->setScore('2nd');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateTieBreak($home,$away,11);
		}
		
		// Tie-break (3rd Set)
		if($each_bet->market_name == "Tie-break (3rd Set)"){
			$this->setScore('3rd');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateTieBreak($home,$away,11);
		}
		
		// Tie-break (4th Set)
		if($each_bet->market_name == "Tie-break (4th Set)"){
			$this->setScore('4th');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateTieBreak($home,$away,11);
		}
		
		// Tie-break (5th Set)
		if($each_bet->market_name == "Tie-break (5th Set)"){
			$this->setScore('5th');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateTieBreak($home,$away,11);
		}
		
		// Odd/Even
		if($each_bet->market_name == "Odd/Even"){
			$this->setWonSets();
			$home = (int)$this->home_set;
			$away = (int)$this->away_set;
			$this->calculateOddEven($home,$away);
		}
		
		// Odd/Even (1st Set)
		if($each_bet->market_name == "Odd/Even (1st Set)"){
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
		
		// Win at least one set (Player 1)
		if($each_bet->market_name == "Win at least one set (Player 1)"){
			$this->setWonSets();
			$home = (int)$this->home_set;
			$this->calculateWonAtleastOneSet($home);
		}
		
		// Win at least one set (Player 2)
		if($each_bet->market_name == "Win at least one set (Player 2)"){
			$this->setWonSets();
			$away = (int)$this->away_set;
			$this->calculateWonAtleastOneSet($away);
		}
		
		// Asian Handicap by Games (1st Set)
		if($each_bet->market_name == "Asian Handicap by Games (1st Set)"){
			$this->setScore('1st');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateAsianHandicap($home,$away);
		}
		
		// Asian Handicap by Games (2nd Set)
		if($each_bet->market_name == "Asian Handicap by Games (2nd Set)"){
			$this->setScore('2nd');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateAsianHandicap($home,$away);
		}
		
		// Asian Handicap by Sets in Match
		if($each_bet->market_name == "Asian Handicap by Sets in Match"){
			$this->setWonSets();
			$home = (int)$this->home_set;
			$away = (int)$this->away_set;
			$this->calculateAsianHandicap($home,$away);
		}
		
		// Asian Handicap by Games in Match
		if($each_bet->market_name == "Asian Handicap by Games in Match"){
			$this->setScore();
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateAsianHandicap($home,$away);
		}
		
		// Set Betting
		if($each_bet->market_name == "Set Betting"){
			$this->setWonSets();
			$home = (int)$this->home_set;
			$away = (int)$this->away_set;
			$this->calculateCorrectScore($home,$away);
		}
		
		// Set / Match
		if($each_bet->market_name == "Set / Match"){
			$this->setWonSets();
			$set_home = (int)$this->home_set;
			$set_away = (int)$this->match_info->away_set;
			$this->setScore();
			$full_home = (int)$this->home_score;
			$full_away = (int)$this->away_score;
			$this->calculateSetMatch($set_home,$set_away,$full_home,$full_away);
		}
		
		// Correct Score (1st Set)
		if($each_bet->market_name == "Correct Score (1st Set)"){
			$this->setScore('1st');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateCorrectScore($home,$away);
		}
		
		// Correct Score (2nd Set)
		if($each_bet->market_name == "Correct Score (2nd Set)"){
			$this->setScore('2nd');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateCorrectScore($home,$away);
		}
		
		// Correct Score (3rd Set)
		if($each_bet->market_name == "Correct Score (3rd Set)"){
			$this->setScore('3rd');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateCorrectScore($home,$away);
		}
		
		// Correct Score (4th Set)
		if($each_bet->market_name == "Correct Score (4th Set)"){
			$this->setScore('4th');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateCorrectScore($home,$away);
		}
		
		// Correct Score (5th Set)
		if($each_bet->market_name == "Correct Score (5th Set)"){
			$this->setScore('5th');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateCorrectScore($home,$away);
		}
		
		// Number of sets
		if($each_bet->market_name == "Number of sets"){
			$this->setWonSets();
			$set_home = (int)$this->home_set;
			$set_away = (int)$this->away_set;
			$this->calculateNumberOfSets($home,$away);
		}
		
		// Will there be the 5th set?
		if($each_bet->market_name == "Will there be the 5th set?"){
			$this->setWonSets();
			$set_home = (int)$this->home_set;
			$set_away = (int)$this->away_set;
			$this->calculateWillBeMentionedSet($home,$away,5);
		}
		
		// Will there be the 4th set?
		if($each_bet->market_name == "Will there be the 4th set?"){
			$this->setWonSets();
			$set_home = (int)$this->home_set;
			$set_away = (int)$this->away_set;
			$this->calculateWillBeMentionedSet($home,$away,4);
		}
		
		// How many games will be played in 1st set?
		if($each_bet->market_name == "How many games will be played in 1st set?"){
			$this->setScore('1st');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateHowManyGamesWillBePlayed($home,$away);
		}
		
		// How many games will be played in 2nd set?
		if($each_bet->market_name == "How many games will be played in 2nd set?"){
			$this->setScore('2nd');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateHowManyGamesWillBePlayed($home,$away);
		}
		
		// How many games will be played in 3rd set?
		if($each_bet->market_name == "How many games will be played in 3rd set?"){
			$this->setScore('3rd');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateHowManyGamesWillBePlayed($home,$away);
		}
		
		// How many games will be played in 4th set?
		if($each_bet->market_name == "How many games will be played in 4th set?"){
			$this->setScore('4th');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateHowManyGamesWillBePlayed($home,$away);
		}
		
		// How many games will be played in 5th set?
		if($each_bet->market_name == "How many games will be played in 5th set?"){
			$this->setScore('4th');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateHowManyGamesWillBePlayed($home,$away);
		}
	}
}
