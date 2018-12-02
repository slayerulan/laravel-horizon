<?php

namespace App\Http\Controllers\BetCalculations;

use Illuminate\Http\Request;
use App\FeedModel\LiveMatch;
/**
 * This will control the calculation of all markets for Football
 *
 * @author Arijit Jana
 */
class LiveFootballCalculation extends LiveBetCalculation
{
	public $first_half_odds 	      	= ['10568'=>'First Half Goals', '10561'=>'Half Time Correct Score', '10161'=>'Half Time Result', '50346'=>'1st Half 3-Way Handicap', '50390'=>'Both Teams to Score in 1st Half'];
	
	public $fulltime_match_status   	= ['Finished'];
	
	public $pre_halftime_match_status	= ['1st Half'];
	
	public $cancelled_match_status		= ['Interrupted'];
	
    public $not_valid_score_type    	= ['','[-]','[ - ]','[]','?','[?-?]','[? - ?]','? - ?'];
	
	public $home_score;
	
    public $away_score;
	
	
    public function footballSingleBetCalculate(){
		$this->singleBetCalculation('Football');
	}
	
	public function footballComboBetCalculate(){
		$this->comboBetCalculation('Football');
	}
	
	// this will check match status to calculate bet
	public function checkMatchStatus() {
		// now we should check match status
		if($this->checkBetCancelStatus() == true && $this->match_info != false && !in_array($this->match_info->period,$this->cancelled_match_status)) {
			// a($this->match_info);
			// print_r($this->match_info->score);echo "<br>";
            $score = explode(":",(string)$this->match_info->score);
			
			if($this->match_info->period == 'Finished' && !in_array($score,$this->not_valid_score_type)){
				$this->calculate_bet = true;
			}
			else if($this->match_info->period == '2nd Half') {
				if((int)$this->odds_set == 1){
					// if it is a 1st half odd
					$this->calculate_bet = true;
				}
				else{
					// if not a 1st half odd
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
	
	// this will check match either cancel or not to calculate bet
	public function checkBetCancelStatus() {
		$match_id = $this->single_bet_info->match_id;
		$match_details = LiveMatch::where('match_id', $match_id)->first();
		if($match_details){
			$this->no_data_found = false;
			$this->match_info = $match_details;
			$this->getOddsSetNumber();
			if($this->match_info->status == "active"){
				return true;
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
	
	// this will set the odds set number
	public function getOddsSetNumber() {
		$market_name = $this->single_bet_info->market_name;
		if(in_array($market_name,$this->first_half_odds)){
			$this->odds_set = 1;
		}
		else{
			// full time odds
			$this->odds_set = 2;
		}
	}
	
	// this will set score of home or away for diff selected
	public function setScore($half = null){
		$scores = explode(":",$this->match_info->score);
		$ht_scores = explode(":",$this->match_info->ht_score);
        if($half == "st"){
            // need to set only second half score
            $ft_score_array = explode(':', $scores);
            $ht_score_array = explode(':', $ht_scores);
            $this->home_score = (int)$ft_score_array[0] - (int)$ht_score_array[0];
            $this->away_score = (int)$ft_score_array[1] - (int)$ht_score_array[1];
        }
		else{
            if($half == "ht"){
                // only half time
				$score = $ht_scores;
            }
			else{
                // only full time
				$score = $scores;
            }
            $this->home_score = (int)$score[0];
            $this->away_score = (int)$score[1];
        }
    }
	
	// this will calculate each market
	public function CalculateEachBet() {
		$each_bet = $this->single_bet_info;

		// Fulltime Result
        if($each_bet->market_name == "Fulltime Result"){
            $this->setScore();
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateMatchWinnerLive($home,$away);
		}
		
		// Double Chance
		if($each_bet->market_name == "Double Chance"){
            $this->setScore();
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateDoubleChanceLive($home,$away);
		}
		
		// Half Time Result
		if($each_bet->market_name == "Half Time Result"){
            $this->setScore('ht');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateMatchWinnerLive($home,$away);
		}
		
		if($each_bet->market_name == "To Qualify"){
            $this->setScore();
			$agg_result = $this->match_info->agg;
			$this->calculateToQualify($agg_result);
		}
		
		// Match Goals
		if($each_bet->market_name == "Match Goals"){
            $this->setScore();
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateOverUnder($home,$away);
		}
		
		// First Half Goals
		if($each_bet->market_name == "First Half Goals"){
            $this->setScore('ht');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateOverUnder($home,$away);
		}
		
		// Half Time/Full Time
		if($each_bet->market_name == "Half Time/Full Time"){
            $this->setScore('ht');
			$ht_home = (int)$this->home_score;
			$ht_away = (int)$this->away_score;
			$this->setScore('st');
			$ft_home = (int)$this->home_score;
			$ft_away = (int)$this->away_score;
			$this->calculateHTFTDoubleLive($ht_home,$ht_away,$ft_home,$ft_away);
		}
		
		// Half Time Correct Score
		if($each_bet->market_name == "Half Time Correct Score"){
            $this->setScore('ht');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateCorrectScore($home,$away);
		}
		
		// Final Score
		if($each_bet->market_name == "Final Score"){
            $this->setScore();
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateCorrectScore($home,$away);
		}
		
		// 3-Way Handicap
		if($each_bet->market_name == "3-Way Handicap"){
            $this->setScore();
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateThreeWayHandicap($home,$away);
		}
		
		// 1st Half 3-Way Handicap
		if($each_bet->market_name == "1st Half 3-Way Handicap"){
            $this->setScore('ht');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateThreeWayHandicap($home,$away);
		}
		
		// Draw No Bet
		if($each_bet->market_name == "Draw No Bet"){
            $this->setScore();
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateDrawNoBet($home,$away);
		}
		
		// Goals Odd/Even
		if($each_bet->market_name == "Goals Odd/Even"){
            $this->setScore();
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateOddEven($home,$away);
		}
		
		// Result / Both Teams To Score
		if($each_bet->market_name == "Result / Both Teams To Score"){
            $this->setScore();
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateResultBothTeamsToScoreLiveMatch($home,$away);
		}
		
		// Both Teams to Score
		if($each_bet->market_name == "Both Teams to Score"){
            $this->setScore();
			$home = (int)$this->home_score;
            $away = (int)$this->away_score;
			$this->calculateBothTeamsToScore($home,$away);
		}
		
		// Both Teams to Score in 1st Half
		if($each_bet->market_name == "Both Teams to Score in 1st Half"){
            $this->setScore('ht');
			$home = (int)$this->home_score;
            $away = (int)$this->away_score;
			$this->calculateBothTeamsToScore($home,$away);
		}
		
		// Both Teams to Score in 2nd Half
		if($each_bet->market_name == "Both Teams to Score in 2nd Half"){
            $this->setScore('st');
			$home = (int)$this->home_score;
            $away = (int)$this->away_score;
			$this->calculateBothTeamsToScore($home,$away);
		}
		
		// Home Team Clean Sheet
		if($each_bet->market_name == "Home Team Clean Sheet"){
            $this->setScore();
			$goal = (int)$this->away_score;
			$this->calculateCleanSheet($goal);
		}

		// Away Team Clean Sheet
        if($each_bet->market_name == "Away Team Clean Sheet"){
            $this->setScore();
			$goal = (int)$this->home_score;
			$this->calculateCleanSheet($goal);
		}
		
		// Home Team Exact Goals
		if($each_bet->market_name == "Home Team Exact Goals"){
            $this->setScore();
			$home = (int)$this->home_score;
			$away = 0;
			$this->calculateExactGoalNumberLive($home,$away);
		}
		
		// Away Team Exact Goals
		if($each_bet->market_name == "Away Team Exact Goals"){
            $this->setScore();
			$home = 0;
			$away = (int)$this->away_score;
			$this->calculateExactGoalNumberLive($home,$away);
		}
		
		// Home Team Goals
		if($each_bet->market_name == "Home Team Goals"){
            $this->setScore();
			$home = (int)$this->home_score;
			$away = 0;
			$this->calculateHomeAwayTeamGoals($home,$away);
		}
		
		// Away Team Goals
		if($each_bet->market_name == "Away Team Goals"){
            $this->setScore();
			$home = 0;
			$away = (int)$this->away_score;
			$this->calculateHomeAwayTeamGoals($home,$away);
		}
		
		// Home Team to Score in Both Halves
		if($each_bet->market_name == "Home Team to Score in Both Halves"){
            $this->setScore('ht');
			$ht_home = (int)$this->home_score;
            $this->setScore('st');
			$ft_home = (int)$this->home_score;
			$this->calculateHomeAwayTeamsToScoreInBothHalves($ht_home,$ft_home);
		}
		
		// Away Team to Score in Both Halves
		if($each_bet->market_name == "Away Team to Score in Both Halves"){
            $this->setScore('ht');
			$ht_away = (int)$this->away_score;
            $this->setScore('st');
			$ft_away = (int)$this->away_score;
			$this->calculateHomeAwayTeamsToScoreInBothHalves($ht_away,$ft_away);
		}
		
		// To Win 2nd Half
		if($each_bet->market_name == "To Win 2nd Half"){
            $this->setScore('st');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateMatchWinnerLive($home,$away);
		}
    }
}