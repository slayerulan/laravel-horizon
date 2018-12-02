<?php

namespace App\Http\Controllers\BetCalculations;

use Illuminate\Http\Request;
use App\Betslip;
use App\FeedModel\Match;
/**
 * This will control the calculation of all markets for Football
 *
 * @author Arijit Jana
 */
class FootballCalculation extends BetCalculation
{
	/**
	 * name of all the markets which can be calculated if first half is over
	 * @var array
	 */
	public $first_half_odds 	      	= ['Asian Handicap (1st Half)','Odd/Even (1st Half)','Correct Score (1st Half)','Both Teams To Score (1st Half)','Draw In 1st Half','Result/Total Goals (1st Half)'];
	
	/**
	 * period value that indicates the match is over
	 * @var array
	 */
	public $fulltime_match_status   	= ['255'];
	
	/**
	 * period value that indicates the match hasn't started yet
	 * @var array
	 */
	public $pre_halftime_match_status   	= ['0', '-1', '1'];

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
	 * calculates all single bets for Football
	 * @return string     returns the time of the calculation
	 */
    public function footballSingleBetCalculate(){
		$this->singleBetCalculation('Football');
	}
	
	/**
	 * calculates all combo bets for Football
	 * @return string     returns the time of the calculation
	 */
	public function footballComboBetCalculate(){
		$this->comboBetCalculation('Football');
	}
	
	/**
	 * this will check match status to calculate bet
	 * @return boolean     true = do calculate / false = don't calculate
	 */
	public function checkMatchStatus() {
		// now we should check match status
		if($this->checkBetCancelStatus() == true && $this->match_info != false && !in_array($this->match_info->period,$this->cancelled_match_status)) {
            $score = explode(" ",$this->match_info->score);
            $ft_score = $score[0];
            if(isset($score[1])) {
				$ht_score = str_replace(")","",str_replace("(","",$score[1]));
			}
			
			if(in_array($this->match_info->period,$this->fulltime_match_status) && !in_array($ft_score,$this->not_valid_score_type)){
				$this->calculate_bet = true;
			}
			else if(in_array($this->match_info->period,$this->pre_halftime_match_status)) {
				$this->calculate_bet = false;
			}
			else{
				// $live_match = is_numeric($this->match_info->period) ? true : false;
				// if($live_match == true){
					// $match_set_in_array = $this->match_info->period > 55 ? 2 : 1 ;
					// if(($match_half > (int)$this->odds_set) &&  !in_array($ht_score,$this->not_valid_score_type)){
						// $this->calculate_bet = true;
					// }else{
						// $this->calculate_bet = false;
					// }
				// }
				if(!in_array($this->match_info->period,$this->pre_halftime_match_status)) {
					// we should calculate first half odds
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
	public function checkBetCancelStatus() {
		$match_id = $this->single_bet_info->match_id;
		// $market_name = $this->single_bet_info->market_name;
		$match_details = Match::where('match_id', $match_id)->where('approved', 1)->first();
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
	
	/**
	 * this will set the odds set number
	 * @return integer     odds set number indicates in which period of the match does the market belongs
	 */
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
	
	/**
	 * this will set score of home or away for a match for the given period
	 * @param string 	$period 	period of the match of whos result is needed
	 */
	public function setScore($half = null){
		$scores = explode(" ",$this->match_info->score);
        if($half == "st"){
            // need to set only second half score
            $ft_score = $scores[0];
            $ht_score = str_replace(")","",str_replace("(","",$scores[1]));
			
            $ft_score_array = explode(':', $ft_score);
            $ht_score_array = explode(':', $ht_score);
            $this->home_score = (int)$ft_score_array[0] - (int)$ht_score_array[0];
            $this->away_score = (int)$ft_score_array[1] - (int)$ht_score_array[1];
        }
		else{
            if($half == "ht"){
                // only half time
				$score = str_replace(")","",str_replace("(","",$scores[1]));
            }
			else{
                // only full time
				$score = $scores[0];
            }
            $score_array = explode(':', $score);
            $this->home_score = (int)$score_array[0];
            $this->away_score = (int)$score_array[1];
        }
    }
	
	/**
	 * this will calculate each market
	 */
	public function CalculateEachBet() {
		$each_bet = $this->single_bet_info;

        if($each_bet->market_name == "1st Half Winner"){
            $this->setScore('ht');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateMatchWinner($home,$away);
		}

        if($each_bet->market_name == "2nd Half Winner"){
            $this->setScore('st');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateMatchWinner($home,$away);
		}

		if($each_bet->market_name == "1st Half Exact Goals Number"){
            $this->setScore('ht');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateExactGoalNumber($home,$away,4,5);
		}

        if($each_bet->market_name == "2nd Half Exact Goals Number"){
            $this->setScore('st');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateExactGoalNumber($home,$away,4,5);
		}

		// Asian Handicap
        if($each_bet->market_name == "Asian Handicap"){
            $this->setScore();
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

		if($each_bet->market_name == "Away Team Exact Goals Number"){
            $this->setScore();
			$home = 0;
			$away = (int)$this->away_score;
			$this->calculateExactGoalNumber($home,$away,2,3);
		}

		if($each_bet->market_name == "Away Team Score a Goal"){
            $this->setScore();
			$goal = (int)$this->away_score;
			$this->calculateScoreAGoal($goal);
		}

		// Both Teams To Score
		if($each_bet->market_name == "Both Teams To Score"){
            $this->setScore();
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateBothTeamToScore($home,$away);
		}

		// Both Teams To Score (1st Half)
        if($each_bet->market_name == "Both Teams To Score (1st Half)"){
            $this->setScore('ht');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateBothTeamToScore($home,$away);
		}

		// Both Teams To Score (2nd Half)
        if($each_bet->market_name == "Both Teams To Score (2nd Half)"){
            $this->setScore('st');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateBothTeamToScore($home,$away);
		}

		// Correct Score
		if($each_bet->market_name == "Correct Score"){
            $this->setScore();
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateCorrectScore($home,$away);
		}

		// Correct Score (1st Half)
        if($each_bet->market_name == "Correct Score (1st Half)"){
            $this->setScore('ht');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateCorrectScore($home,$away);
		}

		// Correct Score (2nd Half)
        if($each_bet->market_name == "Correct Score (2nd Half)"){
            $this->setScore('st');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateCorrectScore($home,$away);
		}

		// Clean Sheet (Home Team)
		if($each_bet->market_name == "Clean Sheet (Home Team)"){
            $this->setScore();
			$goal = (int)$this->away_score;
			$this->calculateCleanSheet($goal);
		}

		// Clean Sheet (Away Team)
        if($each_bet->market_name == "Clean Sheet (Away Team)"){
            $this->setScore();
			$goal = (int)$this->home_score;
			$this->calculateCleanSheet($goal);
		}

		// Double Chance
		if($each_bet->market_name == "Double Chance"){
            $this->setScore();
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateDoubleChance($home,$away);
		}

        if($each_bet->market_name == "Double Chance - 1st Half"){
            $this->setScore('ht');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateDoubleChance($home,$away);
		}

		if($each_bet->market_name == "Exact Goals Number"){
            $this->setScore();
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateExactGoalNumber($home,$away,6,7);
		}

		// Over/Under
		if($each_bet->market_name == "Over/Under"){
            $this->setScore();
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateOverUnder($home,$away);
		}

        if($each_bet->market_name == "Goals Over/Under 1st Half"){
            $this->setScore('ht');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateOverUnder($home,$away);
		}

        if($each_bet->market_name == "Goals Over/Under 2nd Half"){
            $this->setScore('st');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateOverUnder($home,$away);
		}

		if($each_bet->market_name == "Home/Away"){
            $this->setScore();
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateHomeAway($home,$away);
		}

		// Highest Scoring Half
        if($each_bet->market_name == "Highest Scoring Half"){
            $this->setScore('ht');
			$first = (int)$this->home_score + (int)$this->away_score;
            $this->setScore('st');
			$second = (int)$this->home_score + (int)$this->away_score;
			$this->calculateHighestScoringHalf($first,$second);
		}

        if($each_bet->market_name == "HT/FT Double"){
            $this->setScore();
			$ft_home = (int)$this->home_score;
			$ft_away = (int)$this->away_score;
            $this->setScore('ht');
			$ht_home = (int)$this->home_score;
			$ht_away = (int)$this->away_score;
			$this->calculateHTFTDouble($ht_home,$ht_away,$ft_home,$ft_away);
		}

		if($each_bet->market_name == "Handicap Result"){
            $this->setScore();
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateHandicap($home,$away);
		}

        if($each_bet->market_name == "Handicap Result 1st Half"){
            $this->setScore('ht');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateHandicap($home,$away);
		}

		if($each_bet->market_name == "Home Team Exact Goals Number"){
            $this->setScore();
			$home = (int)$this->home_score;
			$away = 0;
			$this->calculateExactGoalNumber($home,$away,2,3);
		}

		if($each_bet->market_name == "Home Team Score a Goal"){
            $this->setScore();
			$goal = (int)$this->home_score;
			$this->calculateScoreAGoal($goal);
		}

		// 1x2
		if($each_bet->market_name == "1x2"){
            $this->setScore();
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateMatchWinner($home,$away);
		}

		// Odd/Even
		if($each_bet->market_name == "Odd/Even"){
            $this->setScore();
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateOddEven($home,$away);
		}

		// Odd/Even (1st Half)
        if($each_bet->market_name == "Odd/Even (1st Half)"){
            $this->setScore('ht');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateOddEven($home,$away);
		}

		// Odd/Even (2nd Half)
        if($each_bet->market_name == "Odd/Even (2nd Half)"){
            $this->setScore('st');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateOddEven($home,$away);
		}
		
		// Home Odd/Even
		if($each_bet->market_name == "Home Odd/Even"){
            $this->setScore();
			$home = (int)$this->home_score;
			$this->calculateHomeOddEven($home);
		}
		
		// Away Odd/Even
		if($each_bet->market_name == "Away Odd/Even"){
            $this->setScore();
			$away = (int)$this->away_score;
			$this->calculateAwayOddEven($away);
		}

		// Result/Total Goals
        if($each_bet->market_name == "Result/Total Goals"){
            $this->setScore();
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateResultTotalGoal($home,$away);
		}

		// Result/Total Goals (1st Half)
        if($each_bet->market_name == "Result/Total Goals (1st Half)"){
            $this->setScore('ht');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateResultTotalGoal($home,$away);
		}

		// Result/Total Goals (2nd Half)
        if($each_bet->market_name == "Result/Total Goals (2nd Half)"){
            $this->setScore('st');
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateResultTotalGoal($home,$away);
		}

		// Results/Both Teams To Score
        if($each_bet->market_name == "Results/Both Teams To Score"){
            $this->setScore();
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateResultBothTeamsToScore($home,$away);
		}

		// To Win Either Half
        if($each_bet->market_name == "To Win Either Half"){
            $this->setScore('ht');
			$ht_home = (int)$this->home_score;
            $ht_away = (int)$this->away_score;
            $this->setScore('st');
			$ft_home = (int)$this->home_score;
            $ft_away =  (int)$this->away_score;
			$this->calculateWinEitherHalf($ht_home,$ht_away,$ft_home,$ft_away);
		}
		
		// Home team will win either half
		if($each_bet->market_name == "Home team will win either half"){
            $this->setScore('ht');
			$ht_home = (int)$this->home_score;
            $ht_away = (int)$this->away_score;
            $this->setScore('st');
			$ft_home = (int)$this->home_score;
            $ft_away =  (int)$this->away_score;
			$this->calculateHomeTeamWillWinEitherHalf($ht_home,$ht_away,$ft_home,$ft_away);
		}
		
		// Away team will win either half
		if($each_bet->market_name == "Away team will win either half"){
            $this->setScore('ht');
			$ht_home = (int)$this->home_score;
            $ht_away = (int)$this->away_score;
            $this->setScore('st');
			$ft_home = (int)$this->home_score;
            $ft_away =  (int)$this->away_score;
			$this->calculateAwayTeamWillWinEitherHalf($ht_home,$ht_away,$ft_home,$ft_away);
		}
		
        if($each_bet->market_name == "Win To Nil"){
            $this->setScore();
			$home = (int)$this->home_score;
			$away = (int)$this->away_score;
			$this->calculateWinToNill($home,$away);
		}

        if($each_bet->market_name == "To Qualify"){
            $this->setScore();
			$agg_result = $this->match_info->agg;
			$this->calculateToQualify($agg_result);
		}

		if($each_bet->market_name == "Total - Home"){
            $this->setScore();
			$home = (int)$this->home_score;
			$this->calculateTotalHomeAway($home);
		}

		if($each_bet->market_name == "Total - Away"){
            $this->setScore();
			$away = (int)$this->away_score;
			$this->calculateTotalHomeAway($away);
		}

		// To win both halves
		if($each_bet->market_name == "To win both halves"){
            $this->setScore('ht');
			$ht_home = (int)$this->home_score;
            $ht_away = (int)$this->away_score;
            $this->setScore('st');
			$ft_home = (int)$this->home_score;
            $ft_away =  (int)$this->away_score;
			$this->calculateWinBothHalves($ht_home,$ht_away,$ft_home,$ft_away);
		}
		
		// Home Win Both Halves
		if($each_bet->market_name == "Home Win Both Halves"){
            $this->setScore('ht');
			$ht_home = (int)$this->home_score;
            $ht_away = (int)$this->away_score;
            $this->setScore('st');
			$ft_home = (int)$this->home_score;
            $ft_away =  (int)$this->away_score;
			$this->calculateHomeWinBothHalves($ht_home,$ht_away,$ft_home,$ft_away);
		}
		
		// Away Win Both Halves
		if($each_bet->market_name == "Away Win Both Halves"){
            $this->setScore('ht');
			$ht_home = (int)$this->home_score;
            $ht_away = (int)$this->away_score;
            $this->setScore('st');
			$ft_home = (int)$this->home_score;
            $ft_away =  (int)$this->away_score;
			$this->calculateAwayWinBothHalves($ht_home,$ht_away,$ft_home,$ft_away);
		}
		
		// Both Teams To Score in Both Halves
		if($each_bet->market_name == "Both Teams To Score in Both Halves"){
            $this->setScore('ht');
			$ht_home = (int)$this->home_score;
            $ht_away = (int)$this->away_score;
            $this->setScore('st');
			$ft_home = (int)$this->home_score;
            $ft_away =  (int)$this->away_score;
			$this->calculateBothTeamsToScoreInBothHalves($ht_home,$ht_away,$ft_home,$ft_away);
		}
		
		// To Score in Both Halves
		if($each_bet->market_name == "To Score in Both Halves"){
            $this->setScore('ht');
			$ht_home = (int)$this->home_score;
            $ht_away = (int)$this->away_score;
            $this->setScore('st');
			$ft_home = (int)$this->home_score;
            $ft_away =  (int)$this->away_score;
			$this->calculateToScoreInBothHalves($ht_home,$ht_away,$ft_home,$ft_away);
		}
		
		// Draw In 1st Half
		if($each_bet->market_name == "Draw In 1st Half"){
            $this->setScore('ht');
			$home = (int)$this->home_score;
            $away = (int)$this->away_score;
			$this->calculateDrawInFirstHalf($home,$away);
		}
		
		// Draw In Either Half
		if($each_bet->market_name == "Draw In Either Half"){
            $this->setScore('ht');
			$ht_home = (int)$this->home_score;
            $ht_away = (int)$this->away_score;
            $this->setScore('st');
			$ft_home = (int)$this->home_score;
            $ft_away =  (int)$this->away_score;
			$this->calculateDrawInEitherHalf($ht_home,$ht_away,$ft_home,$ft_away);
		}
		
		// Home Highest Scoring Half
		if($each_bet->market_name == "Home Highest Scoring Half"){
            $this->setScore('ht');
			$ht_home = (int)$this->home_score;
            $this->setScore('st');
			$ft_home = (int)$this->home_score;
			$this->calculateHomeHighestScoringHalf($ht_home,$ft_home);
		}
		
		// Away Highest Scoring Half
		if($each_bet->market_name == "Away Highest Scoring Half"){
            $this->setScore('ht');
			$ht_away = (int)$this->away_score;
            $this->setScore('st');
			$ft_away = (int)$this->away_score;
			$this->calculateAwayHighestScoringHalf($ht_away,$ft_away);
		}
    }
}