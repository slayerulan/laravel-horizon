<?php
namespace App\Http\Traits\BetCalculations;
/**
 * This will contain all market calculation formula
 *
 * @author Arijit Jana
 */
trait CalculationFormula
{
	// this will calculate asian handicap market and return status
    public function calculateAsianHandicap($home,$away) {
		if(strtolower($this->single_bet_info->bet_for) == "1"){
			$f = ($home - $away ) + (float)$this->single_bet_info->extra_value;
		}
		else{
			$f = ($away - $home ) + (float)$this->single_bet_info->extra_value;
		}
		$this->checkingWithF($f);
	}
	// this will calculate asian handicap market and return status
    public function calculateThreeWayHandicap($home,$away) {
		$result = 0;
		if(strtolower($this->single_bet_info->bet_for) == "home"){
			$f = ($home - $away ) + (float)$this->single_bet_info->extra_value;
			if($f > 0) {
				$result = 1;
			}
		}
		else if(strtolower($this->single_bet_info->bet_for) == "away"){
			$f = ($away - $home ) + (float)$this->single_bet_info->extra_value;
			if($f > 0) {
				$result = 1;
			}
		}
		else{
			$f = ($home - $away ) + (float)$this->single_bet_info->extra_value;
			if($f == 0) {
				$result = 1;
			}
		}
		if($result == 1) {
			$this->BetWin();
		}
		else{
			$this->BetLoss();
		}
	}
	// this will calculate both team to score market
	public function calculateBothTeamToScore($home,$away) {
		if($home > 0 && $away > 0 && strtolower($this->single_bet_info->bet_for) == "yes"){
			$this->BetWin();
		}
		else if(($home == 0 || $away == 0) && strtolower($this->single_bet_info->bet_for) == "no"){
			$this->BetWin();
		}
		else{
			$this->BetLoss();
		}
	}
	public function calculateHandicap($home,$away){
		//this will calculate handicap market and return status
		if(strtolower($this->single_bet_info->bet_for) == "2"){
            $f = ($away - $home ) + (float)$this->single_bet_info->extra_value;
		}else{
            $f = ($home - $away ) + (float)$this->single_bet_info->extra_value;
		}
		if(strtolower($this->single_bet_info->bet_for) == "x" || strtolower($this->single_bet_info->bet_for) == "tie"){
			if ($f == 0) {
				$this->BetWin();
			}else{
				$this->BetLoss();
			}
		}else{
			//Home or Away
			if ($f > 0) {
				$this->BetWin();
			}else{
				$this->BetLoss();
			}
		}
	}
	// this will calculate asian handicap market and return status
	public function calculateOverUnder($home,$away){
		if(strtolower($this->single_bet_info->bet_for) == "over"){
			$f = ($home + $away ) - (float)$this->single_bet_info->extra_value;
		}
		else{
			$f = (float)$this->single_bet_info->extra_value - ($away + $home );
		}
		$this->checkingWithF($f);
	}
	public function calculateHomeAwayTeamGoals($goal,$away){
		if(strtolower($this->single_bet_info->bet_for) == "over"){
			$f = ($home + $away ) - (float)$this->single_bet_info->extra_value;
		}
		else{
			$f = (float)$this->single_bet_info->extra_value - ($away + $home );
		}
		$this->checkingWithF($f);
	}
	public function calculateTotalHomeAway($score){
		//this will calculate Total-Home & Total-Away and return status
		if(strtolower($this->single_bet_info->bet_for) == "over"){
			$f = $score  - (float)$this->single_bet_info->extra_value;
		}else{
			$f = (float)$this->single_bet_info->extra_value - $score;
		}
		$this->checkingWithF($f);
	}
	// this will calculate correct score market
	public function calculateCorrectScore($home,$away,$separetor =":") {
		$your_choice = $this->single_bet_info->bet_for;
		$your_choice = explode($separetor, $your_choice);
		if (((int)$home == (int)$your_choice[0]) && ((int)$away == (int)$your_choice[1])) {
			$this->BetWin();
		}
		else{
			$this->BetLoss();
		}
	}
    public function calculateExactGoalNumber($home,$away,$checking,$more_muber) {
        $total_goal = $home + $away;
        if($total_goal > $checking) {
            $total_goal = "more ".$more_muber;
        }
		else{
            $total_goal = $total_goal;
        }
        if((string)$this->single_bet_info->bet_for == (string)$total_goal) {
			$this->BetWin();
		}
		else{
			$this->BetLoss();
		}
    }
	public function calculateExactGoalNumberLive($home,$away) {
        $total_goal = $home + $away;
        if($total_goal == 0) {
            $total_goal = "NO_GOAL";
        }
		else{
            $total_goal = $total_goal;
        }
        if((string)$this->single_bet_info->bet_for == (string)$total_goal) {
			$this->BetWin();
		}
		else{
			$this->BetLoss();
		}
    }
	// this will calculate clean sheet home/away
    public function calculateCleanSheet($goal){
        if(strtolower($this->single_bet_info->bet_for) == "yes" && $goal == 0){
            $this->BetWin();
        }
		else if(strtolower($this->single_bet_info->bet_for) == "no" && $goal > 0){
            $this->BetWin();
        }
		else{
            $this->BetLoss();
        }
    }
    public function calculateScoreAGoal($goal){
        //this will calculate clean sheet home/away
        if(strtolower($this->single_bet_info->bet_for) == "yes" && $goal > 0){
            $this->BetWin();
        }else if(strtolower($this->single_bet_info->bet_for) == "no" && $goal == 0){
            $this->BetWin();
        }else{
            $this->BetLoss();
        }
    }
	// this will calculate if at least a run is scored
	public function calculateScoreARun($home,$away){
		$run = $home + $away;
        if(strtolower($this->single_bet_info->bet_for) == "yes" && $run > 0){
            $this->BetWin();
        }
		else if(strtolower($this->single_bet_info->bet_for) == "no" && $run == 0){
            $this->BetWin();
        }
		else{
            $this->BetLoss();
        }
    }
	// this will calculate home away market
	public function calculateHomeAway($home,$away){
		if($home == $away){
			// match draw
			$this->BetReturnSteck();
		}
		else{
			if(($home > $away) && $this->single_bet_info->bet_for == "1"){
				$this->BetWin();
			}
			else if(($home < $away) && $this->single_bet_info->bet_for == "2"){
				$this->BetWin();
			}
			else{
				$this->BetLoss();
			}
		}
	}
    public function calculateWinBothHalves($home_ht,$away_ht,$home_ft,$away_ft){
        if(($home_ht > $away_ht && $home_ft > $away_ft) && strtolower($this->single_bet_info->bet_for) == "1"){
            $this->BetWin();
        }
		else if(($home_ht < $away_ht && $home_ft < $away_ft) && strtolower($this->single_bet_info->bet_for) == "2"){
            $this->BetWin();
        }
		else{
            $this->BetLoss();
        }
    }
	public function calculateHomeWinBothHalves($home_ht,$away_ht,$home_ft,$away_ft){
        if(($home_ht > $away_ht && $home_ft > $away_ft) && strtolower($this->single_bet_info->bet_for) == "yes"){
            $this->BetWin();
        }
		else if(($home_ht < $away_ht && $home_ft < $away_ft) && strtolower($this->single_bet_info->bet_for) == "no"){
            $this->BetWin();
        }
		else{
            $this->BetLoss();
        }
    }
	public function calculateAwayWinBothHalves($home_ht,$away_ht,$home_ft,$away_ft){
        if(($home_ht < $away_ht && $home_ft < $away_ft) && strtolower($this->single_bet_info->bet_for) == "yes"){
            $this->BetWin();
        }
		else if(($home_ht > $away_ht && $home_ft > $away_ft) && strtolower($this->single_bet_info->bet_for) == "no"){
            $this->BetWin();
        }
		else{
            $this->BetLoss();
        }
    }
	public function calculateHomeWinAllQuarters($score) {
		if(($score['home_first'] > $score['away_first'] && $score['home_second'] > $score['away_second'] && $score['home_third'] > $score['away_third'] && $score['home_forth'] > $score['away_forth']) && strtolower($this->single_bet_info->bet_for) == "yes"){
            $this->BetWin();
        }
		else if(($score['home_first'] > $score['away_first'] && $score['home_second'] > $score['away_second'] && $score['home_third'] > $score['away_third'] && $score['home_forth'] > $score['away_forth']) && strtolower($this->single_bet_info->bet_for) == "no"){
            $this->BetWin();
        }
		else{
            $this->BetLoss();
        }
	}
	public function calculateAwayWinAllQuarters($score) {
		if(($score['home_first'] < $score['away_first'] && $score['home_second'] < $score['away_second'] && $score['home_third'] < $score['away_third'] && $score['home_forth'] < $score['away_forth']) && strtolower($this->single_bet_info->bet_for) == "yes"){
            $this->BetWin();
        }
		else if(($score['home_first'] < $score['away_first'] && $score['home_second'] < $score['away_second'] && $score['home_third'] < $score['away_third'] && $score['home_forth'] < $score['away_forth']) && strtolower($this->single_bet_info->bet_for) == "no"){
            $this->BetWin();
        }
		else{
            $this->BetLoss();
        }
	}
	// this will check whether both team scored in both halves
    public function calculateBothTeamsToScoreInBothHalves($home_ht,$away_ht,$home_ft,$away_ft) {
		if($home_ht > 0 && $away_ht > 0 && $home_ft > 0 && $away_ft > 0) {
			$both_team_scored_in_both_half = 'yes';
		}
		else{
			$both_team_scored_in_both_half = 'no';
		}
		if(strtolower($this->single_bet_info->bet_for) == $both_team_scored_in_both_half) {
			$this->BetWin();
		}
		else{
			$this->BetLoss();
		}
    }
	public function calculateHomeAwayTeamsToScoreInBothHalves($ht_score,$ft_score) {
		if($ht_score > 0 && $ft_score > 0) {
			$team_scored_in_both_half = 'yes';
		}
		else{
			$team_scored_in_both_half = 'no';
		}
		if(strtolower($this->single_bet_info->bet_for) == $team_scored_in_both_half) {
			$this->BetWin();
		}
		else{
			$this->BetLoss();
		}
    }
	public function calculateBothTeamsToScore($home,$away) {
		if($home > 0 && $away > 0) {
			$both_team_scored = 'yes';
		}
		else{
			$both_team_scored = 'no';
		}
		if(strtolower($this->single_bet_info->bet_for) == $both_team_scored) {
			$this->BetWin();
		}
		else{
			$this->BetLoss();
		}
    }
	// this will check if scored in both halves
    public function calculateToScoreInBothHalves($home_ht,$away_ht,$home_ft,$away_ft) {
		if(($home_ht > 0 || $away_ht > 0) && ($home_ft > 0 || $away_ft > 0)) {
			$scored_in_both_halves = 'yes';
		}
		else{
			$scored_in_both_halves = 'no';
		}
		if(strtolower($this->single_bet_info->bet_for) == $scored_in_both_halves) {
			$this->BetWin();
		}
		else{
			$this->BetLoss();
		}
    }
    public function calculateWinEitherHalf($home_ht,$away_ht,$home_ft,$away_ft){
        if(($home_ht > $away_ht || $home_ft > $away_ft) && strtolower($this->single_bet_info->bet_for) == "1"){
            $this->BetWin();
        }else if(($home_ht < $away_ht || $home_ft < $away_ft) && strtolower($this->single_bet_info->bet_for) == "2"){
            $this->BetWin();
        }else{
            $this->BetLoss();
        }
    }
	// this will check whether Home team won in either of the halves
	public function calculateHomeTeamWillWinEitherHalf($home_ht,$away_ht,$home_ft,$away_ft){
        if(($home_ht > $away_ht || $home_ft > $away_ft) && strtolower($this->single_bet_info->bet_for) == "yes"){
            $this->BetWin();
        }
		else if(($home_ht < $away_ht || $home_ft < $away_ft) && strtolower($this->single_bet_info->bet_for) == "no"){
            $this->BetWin();
        }
		else{
            $this->BetLoss();
        }
    }
	// this will check whether Away team won in either of the halves
	public function calculateAwayTeamWillWinEitherHalf($home_ht,$away_ht,$home_ft,$away_ft){
        if(($home_ht < $away_ht || $home_ft < $away_ft) && strtolower($this->single_bet_info->bet_for) == "yes"){
            $this->BetWin();
        }
		else if(($home_ht > $away_ht || $home_ft > $away_ft) && strtolower($this->single_bet_info->bet_for) == "no"){
            $this->BetWin();
        }
		else{
            $this->BetLoss();
        }
    }
	// this will check whether Draw In 1st Half
	public function calculateDrawInFirstHalf($home,$away){
        if(($home == $away) && strtolower($this->single_bet_info->bet_for) == "yes"){
            $this->BetWin();
        }
		else if(($home != $away) && strtolower($this->single_bet_info->bet_for) == "no"){
            $this->BetWin();
        }
		else{
            $this->BetLoss();
        }
    }
	// this will check whether Draw In Either Half
	public function calculateDrawInEitherHalf($ht_home,$ht_away,$ft_home,$ft_away){
		if($ht_home == $ht_away || $ft_home == $ft_away) {
			$draw_in_either_half = 'yes';
		}
		else{
			$draw_in_either_half = 'no';
		}
        if(strtolower($this->single_bet_info->bet_for) == $draw_in_either_half){
            $this->BetWin();
        }
		else{
            $this->BetLoss();
        }
    }
    public function calculateWinToNill($home,$away){
        if(($home > $away && $away ==  0) && strtolower($this->single_bet_info->bet_for) == "home"){
            $this->BetWin();
        }else if(($home < $away && $home == 0) && strtolower($this->single_bet_info->bet_for) == "away"){
            $this->BetWin();
        }else{
            $this->BetLoss();
        }
    }
    public function calculateToQualify($agg_result){
        if($this->single_bet_info->bet_for == $agg_result){
            $this->BetWin();
        }else if($agg_result == "nill"){
            $this->BetWin();
        }else{
            $this->BetLoss();
        }
    }
	public function calculateTennisHomeAway($home,$away){
		//this will calculate home away market
		if($home == "True" && strtolower($this->single_bet_info->bet_for) == "home"){
			$this->BetWin();
		}
		else if($away == "True" && strtolower($this->single_bet_info->bet_for) == "away"){
			$this->BetWin();
		}
		else{
			$this->BetLoss();
		}
	}
	// this will calculate Ht/Ft odds
	public function calculateHTFT($home_ht,$away_ht,$home_ft,$away_ft){
		if($home_ht == $away_ht){
			$result_ht = 'X';
		}
		else if($home_ht > $away_ht){
			$result_ht = '1';
		}
		else{
			$result_ht = '2';
		}
		if($home_ft == $away_ft){
			$result_ft = 'X';
		}
		else if($home_ft > $away_ft){
			$result_ft = '1';
		}
		else{
			$result_ft = '2';
		}
		$bet_for = explode('/', $this->single_bet_info->bet_for);
		if($result_ht == $bet_for[0] && $result_ft == $bet_for[1]) {
			$this->BetWin();
		}
		else{
			$this->BetLoss();
		}
	}
	// this will calculate Ht/Ft odds
	public function calculateHTFTDouble($home_ht,$away_ht,$home_ft,$away_ft){
		if($home_ht == $away_ht){
			$result_ht = 'X';
		}
		else if($home_ht > $away_ht){
			$result_ht = '1';
		}
		else{
			$result_ht = '2';
		}
		if($home_ft == $away_ft){
			$result_ft = 'X';
		}
		else if($home_ft > $away_ft){
			$result_ft = '1';
		}
		else{
			$result_ft = '2';
		}
		$bet_for = explode('/', $this->single_bet_info->bet_for);
		if($result_ht == $bet_for[0] && $result_ft == $bet_for[1]) {
			$this->BetWin();
		}
		else{
			$this->BetLoss();
		}
	}
	public function calculateHTFTDoubleLive($home_ht,$away_ht,$home_ft,$away_ft){
		if($home_ht == $away_ht){
			$result_ht = 'x';
		}
		else if($home_ht > $away_ht){
			$result_ht = 'home';
		}
		else{
			$result_ht = 'away';
		}
		if($home_ft == $away_ft){
			$result_ft = 'x';
		}
		else if($home_ft > $away_ft){
			$result_ft = 'home';
		}
		else{
			$result_ft = 'away';
		}
		$bet_for = explode('/', $this->single_bet_info->bet_for);
		if($result_ht == strtolower($bet_for[0]) && $result_ft == strtolower($bet_for[1])) {
			$this->BetWin();
		}
		else{
			$this->BetLoss();
		}
	}
	// this will calculate match winner odd
	public function calculateMatchWinner($home,$away){
		if($home == $away){
			$correct_result = 'X';
		}
		else if($home > $away){
			$correct_result = '1';
		}
		else{
			$correct_result = '2';
		}
		if($this->single_bet_info->bet_for == $correct_result){
			$this->BetWin();
		}
		else{
			$this->BetLoss();
		}
	}
	// this will calculate match winner odd for live
	public function calculateMatchWinnerLive($home,$away){
		if($home == $away){
			$correct_result = 'x';
		}
		else if($home > $away){
			$correct_result = 'home';
		}
		else{
			$correct_result = 'away';
		}
		if(strtolower($this->single_bet_info->bet_for) == $correct_result){
			$this->BetWin();
		}
		else{
			$this->BetLoss();
		}
	}
	// this will calculate match winner odd
	public function calculateDoubleChance($home,$away){
		if($home == $away){
			$correct_result = 'X';
		}
		else if($home > $away){
			$correct_result = '1';
		}
		else{
			$correct_result = '2';
		}
		
		if(strpos($this->single_bet_info->bet_for,$correct_result) !== false){
			$this->BetWin();
		}
		else{
			$this->BetLoss();
		}
	}
	public function calculateDoubleChanceLive($home,$away){
		if($home == $away){
			$correct_result = 'X';
		}
		else if($home > $away){
			$correct_result = 'Home';
		}
		else{
			$correct_result = 'Away';
		}
		if(strpos($this->single_bet_info->bet_for,$correct_result) !== false){
			$this->BetWin();
		}
		else{
			$this->BetLoss();
		}
	}
	// this will calculate Home Highest Scoring Half
	public function calculateHomeHighestScoringHalf($ht_home,$ft_home){
		if($ht_home == $ft_home){
			$correct_result = 'x';
		}
		else if($ht_home > $ft_home){
			$correct_result = '1st half';
		}
		else{
			$correct_result = '2nd half';
		}
		if($correct_result == strtolower($this->single_bet_info->bet_for)){
			$this->BetWin();
		}
		else{
			$this->BetLoss();
		}
	}
	// this will calculate Away Highest Scoring Half
	public function calculateAwayHighestScoringHalf($ht_away,$ft_away){
		if($ht_away == $ft_away){
			$correct_result = 'x';
		}
		else if($ht_away > $ft_away){
			$correct_result = '1st half';
		}
		else{
			$correct_result = '2nd half';
		}
		if($correct_result == strtolower($this->single_bet_info->bet_for)){
			$this->BetWin();
		}
		else{
			$this->BetLoss();
		}
	}
	// it will calculate Home odd/even market
	public function calculateHomeOddEven($home){
		$total_goal = $home;
		$this->calculateOddEvenEach($total_goal);
	}
	// it will calculate Away odd/even market
	public function calculateAwayOddEven($away){
		$total_goal = $away;
		$this->calculateOddEvenEach($total_goal);
	}
	// it will calculate odd/even market
	public function calculateOddEven($home,$away){
		$total_goal = $home + $away;
		$this->calculateOddEvenEach($total_goal);
	}
	public function calculateOddEvenEach($total_goal){
		$check = $total_goal % 2;
		if (strtolower($this->single_bet_info->bet_for) == 'odd' || strtolower($this->single_bet_info->bet_for) == 'odds') {
			if ($check != 0) {
				$this->BetWin();
			}
			else {
				$this->BetLoss();
			}
		}
		if (strtolower($this->single_bet_info->bet_for) == 'even') {
			if ($check == 0) {
				$this->BetWin();
			}
			else {
				$this->BetLoss();
			}
		}
	}
	public function calculateWinningMargin($first,$second){
		//this will calculate all winning margin odds
		$f = $first - $second;
		if($f > 0){
			if(strpos($this->single_bet_info->bet_for,'-')){
				$range = explode('-',$this->single_bet_info->bet_for);
				if($f >= (int)$range[0] && $f <= (int)$range[1]){
					$this->BetWin();
				}else{
					$this->BetLoss();
				}
			}else{
				$range = str_ireplace('+','',$this->single_bet_info->bet_for);
				if($f >= (int)$range){
					$this->BetWin();
				}else{
					$this->BetLoss();
				}
			}
		}else{
			$this->BetLoss();
		}
	}
	// this will calculate the highest scoring half
	public function calculateHighestScoringHalf($first,$second){
		if($first == $second){
			$result = "x";
		}
		else if($first > $second){
			$result = "1st half";
		}
		else{
			$result = "2nd half";
		}
		if(strtolower($this->single_bet_info->bet_for) == $result){
			$this->BetWin();
		}
		else{
			$this->BetLoss();
		}
	}
	public function calculateHighestScoringQuarter($total_score){
		$maximum_score = max($total_score);
		if($total_score[$this->single_bet_info->bet_for] == (int)$maximum_score){
			$this->BetWin();
		}
		else{
			$this->BetLoss();
		}
	}
	public function calculateHighestSocrerTeam($all_scores_Home,$all_scores_Away,$highest_score){
		$score_array = 'all_scores_'.$this->single_bet_info->bet_for;
		if(in_array($highest_score,$score_array)){
			$this->BetWin();
		}
		else{
			$this->BetLoss();
		}
	}
	public function calculateOverTime($home_qtr,$away_qtr,$home_ft,$away_ft){
		if($home_qtr != $home_ft || $away_qtr != $away_ft){
			$result = 'yes';
		}
		else{
			$result = 'no';
		}
		if(strtolower($this->single_bet_info->bet_for) == $result){
			$this->BetWin();
		}
		else{
			$this->BetLoss();
		}
	}
	// this will calculate tie break market
	public function calculateTieBreak($home,$away,$condition){
		$total = $home + $away;
		if($total == 0){
			$this->BetReturnSteck();
		}
		else{
			if($total > $condition && strtolower($this->single_bet_info->bet_for) == "yes"){
				$this->BetWin();
			}
			else if($total <= $condition && strtolower($this->single_bet_info->bet_for) == "no"){
				$this->BetWin();
			}
			else{
				$this->BetLoss();
			}
		}
	}
	public function calculateARun($home,$away,$condition){
		//this will calculate tie break market
		$total = $home + $away;
		if($total >= $condition && strtolower($this->single_bet_info->bet_for) == "yes"){
			$this->BetWin();
		}else if($total < $condition && strtolower($this->single_bet_info->bet_for) == "no"){
			$this->BetWin();
		}else{
			$this->BetLoss();
		}
	}
	public function calculateExtraInnings($home,$away){
		//this will calculate tie break market
		if($home && $away && strtolower($this->single_bet_info->bet_for) == "yes"){
			$this->BetWin();
		}
		else if($home == 0 && $away == 0 && strtolower($this->single_bet_info->bet_for) == "no"){
			$this->BetWin();
		}
		else{
			$this->BetLoss();
		}
	}
	public function calculateIfExtraInnings($exrta_innings) {
		if($exrta_innings == true && strtolower($this->single_bet_info->bet_for) == "yes") {
			$this->BetWin();
		}
		else if($exrta_innings == false && strtolower($this->single_bet_info->bet_for) == "no"){
			$this->BetWin();
		}
		else{
			$this->BetLoss();
		}
	}
	public function calculateNumberOfSets($home,$away){
		$total = $home + $away;
		if($total < 2){
			$this->BetReturnSteck();
		}
		else if($total > 3){
			$this->BetLoss();
		}
		else{
			// either 2 or 3
			$bet_option = $total. ' Sets';
			if($bet_option == $this->single_bet_info->bet_for){
				$this->BetWin();
			}
			else{
				$this->BetLoss();
			}
		}
	}
	// calculate bet for correct number of sets for volleyball
	public function calculateCorrectNumberOfSets($home,$away){
		$total = $home + $away;
		if($total < 3){
			$this->BetReturnSteck();
		}
		else{
			// either 3, 4 or 5
			$bet_option = $total.' sets';
			if($bet_option == strtolower($this->single_bet_info->bet_for)){
				$this->BetWin();
			}
			else{
				$this->BetLoss();
			}
		}
	}
	public function calculateSetMatch($set_home,$set_away,$total_home,$total_away){
		if($set_home > $set_away){
			$set_winner = 1;
		}
		else{
			$set_winner = 2;
		}
		if($total_home > $total_away){
			$match_winner = 1;
		}
		else{
			$match_winner = 2;
		}
		$bet_for = explode('/', $this->single_bet_info->bet_for);
		if($set_winner == $bet_for[0] && $match_winner == $bet_for[1]) {
			$this->BetWin();
		}
		else{
			$this->BetLoss();
		}
	}
	// this will calculate result/Total Goals market
	public function calculateResultTotalGoal($home,$away){
		$goal = $this->single_bet_info->extra_value;
		$total_goal = $home + $away;
		if($home == $away){
			$result = 'x';
		}
		else if($home > $away){
			$result = "1";
		}
		else{
			$result = "2";
		}
		if($total_goal > $goal){
			$goal_status = "o";
		}
		else{
			$goal_status = "u";
		}
		$bet_for = explode('/', $this->single_bet_info->bet_for);
		if($result == $bet_for[0] && $goal_status == strtolower($bet_for[1])) {
			$this->BetWin();
		}
		else{
			$this->BetLoss();
		}
	}
	// this will calculate result/Total Goals market
	public function calculateResultBothTeamsToScore($home,$away){
		$total_goal = $home + $away;
		if($home == $away){
			$result = 'x';
		}
		else if($home > $away){
			$result = "1";
		}
		else{
			$result = "2";
		}
		if($home > 0 && $away > 0){
			$goal_status = "yes";
		}
		else{
			$goal_status = "no";
		}
		$bet_for = explode('/', $this->single_bet_info->bet_for);
		if($result == $bet_for[0] && $goal_status == strtolower($bet_for[1])) {
			$this->BetWin();
		}
		else{
			$this->BetLoss();
		}
	}
	public function calculateResultBothTeamsToScoreLiveMatch($home,$away){
		$total_goal = $home + $away;
		if($home == $away){
			$result = 'draw';
		}
		else if($home > $away){
			$result = "home";
		}
		else{
			$result = "away";
		}
		if($home > 0 && $away > 0){
			$goal_status = "yes";
		}
		else{
			$goal_status = "no";
		}
		$bet_for = explode('/', $this->single_bet_info->bet_for);
		if($result == strtolower($bet_for[0]) && $goal_status == strtolower($bet_for[1])) {
			$this->BetWin();
		}
		else{
			$this->BetLoss();
		}
	}
	public function calculateWillGoToOvertime($overtime) {
		if($overtime == true && strtolower($this->single_bet_info->bet_for) == "yes"){
			$this->BetWin();
		}
		else if($overtime == false && strtolower($this->single_bet_info->bet_for) == "no"){
			$this->BetWin();
		}
		else{
			$this->BetLoss();
		}
	}
	public function calculateHighestScoringPeriod($fp_home,$fp_away,$sp_home,$sp_away,$tp_home,$tp_away) {
		$f_period = $fp_home + $fp_away;
		$s_period = $sp_home + $sp_away;
		$t_period = $tp_home + $tp_away;
		if($f_period > $s_period && $f_period > $t_period) {
			$correct_result = '1st period';
		}
		else if($s_period > $f_period && $s_period > $t_period) {
			$correct_result = '2nd period';
		}
		else if($t_period > $f_period && $t_period > $s_period) {
			$correct_result = '3rd period';
		}
		else if($f_period = $s_period && $s_period = $t_period) {
			$correct_result = 'x';
		}
		else{
			$correct_result = 'two period have same score';
		}
		if(strtolower($this->single_bet_info->bet_for) == $correct_result) {
			$this->BetWin();
		}
		else{
			$this->BetLoss();
		}
	}
	// will calculate player won at least one set
	public function calculateWonAtleastOneSet($won_sets){
        if(strtolower($this->single_bet_info->bet_for) == "yes" && $won_sets > 0){
            $this->BetWin();
        }
		else if(strtolower($this->single_bet_info->bet_for) == "no" && $won_sets == 0){
            $this->BetWin();
        }
		else{
            $this->BetLoss();
        }
    }
	// will calculate if there will be a mentioned set
	public function calculateWillBeMentionedSet($home,$away,$set){
		$total_sets = $home + $away;
        if(strtolower($this->single_bet_info->bet_for) == "yes" && $total_sets = $set){
            $this->BetWin();
        }
		else if(strtolower($this->single_bet_info->bet_for) == "no" && $total_sets < $set){
            $this->BetWin();
        }
		else{
            $this->BetLoss();
        }
    }
	// will calculate if there will be a mentioned set
	public function calculateHowManyGamesWillBePlayed($home,$away){
		$total_games = $home + $away;
        if($total_games >= 6 && $total_games < 9){
            $correct_result = '6-8';
        }
		else if($total_games >= 9 && $total_games < 11){
            $correct_result = '9-10';
        }
		else{
            $correct_result = '11+';
        }
		if($this->single_bet_info->bet_for == $correct_result){
			$this->BetWin();
		}
		else{
			$this->BetLoss();
		}
    }
	public function calculateFirstSetAndMatch($home_fs,$away_fs,$home,$away) {
		if($home_fs > $away_fs){
			$first_set_result = 1;
		}
		else if($away_fs > $home_fs){
			$first_set_result = 2;
		}
		else{
			$first_set_result = 'x';
		}
		if($home > $away){
			$match_result = 1;
		}
		else if($away > $home){
			$match_result = 2;
		}
		else{
			$match_result = 'x';
		}
		$bet_for = explode('/', $this->single_bet_info->bet_for);
		if($first_set_result == $bet_for[0] && $match_result == $bet_for[1]){
			$this->BetWin();
		}
		else{
			$this->BetLoss();
		}
	}
	public function calculateTotalNumberOfGoalsInMatch($home,$away) {
		$result = $home + $away;
		if($result == $this->single_bet_info->bet_for){
			$this->BetWin();
		}
		else{
			$this->BetLoss();
		}
	}
	public function calculateDrawNoBet($home,$away) {
		if ($home == $away) {
			$this->BetReturnSteck();
		}
		else {
			if (strtolower($this->single_bet_info->bet_for) == 'home' && $home > $away) {
				$this->BetWin();
			}
			else if (strtolower($this->single_bet_info->bet_for) == 'away' && $home < $away) {
				$this->BetWin();
			}
			else {
				$this->BetLoss();
			}
		}
	}
	public function BetWin(){
		if($this->bet_type == "single") {
			$amount = $this->single_bet_info->stake_amount * $this->single_bet_info->odds_value;
			$this->return_amount = floor($amount);
			$this->win_or_loss = 1;
			$this->bet_status = 'win';
		}
		else{
			$this->result_status['' . $this->single_bet_info->match_id . '@' . $this->single_bet_info->market_name . ''] = 1;
			$this->changeOddValue($this->single_bet_info->odds_value,'win');
		}
	}
	public function BetLoss(){
		if($this->bet_type == "single") {
			$this->return_amount = 0;
			$this->win_or_loss = 0;
			$this->bet_status = 'lost';
		}
		else{
			$this->result_status['' . $this->single_bet_info->match_id . '@' . $this->single_bet_info->market_name . ''] = 2;
			$this->changeOddValue(0,'lost');
		}
	}
	public function BetReturnSteck() {
		if($this->bet_type == "single"){
			$this->return_amount = $this->single_bet_info->stake_amount;
			$this->win_or_loss = 1;
			$this->bet_status = 'return_stake';
		}
		else{
			$this->result_status['' . $this->single_bet_info->match_id . '@' . $this->single_bet_info->market_name . ''] = 5;
			$this->changeOddValue(1,'return_stake');
		}
	}
	public function BetHalfLoss(){
		if($this->bet_type == "single"){
			$amount = $this->single_bet_info->stake_amount;
			$amount = $amount / 2;
			$this->return_amount = $amount;
			$this->win_or_loss = 1;
			$this->bet_status = 'half_lost';
		}
		else{
			$this->result_status['' . $this->single_bet_info->match_id . '@' . $this->single_bet_info->market_name . ''] = 4;
			$this->changeOddValue(((float)$this->single_bet_info->odds_value * .5),'half_lost');
		}
	}
	public function BetHalfWin(){
		if($this->bet_type == "single"){
			$amount = $this->single_bet_info->stake_amount;
			$amount = $amount / 2;
			$amount = $amount + ($amount * $this->single_bet_info->odds_value);
			$this->return_amount = $amount;
			$this->win_or_loss = 1;
			$this->bet_status = 'half_win';
		}
		else{
			$this->result_status['' . $this->single_bet_info->match_id . '@' . $this->single_bet_info->market_name . ''] = 4;
            $new_odd_value  = (((float)$this->single_bet_info->odds_value + 1) * .5);
			$this->changeOddValue($new_odd_value,'half_win');
		}
	}
	// this will checking f to set half win,half loss, return stack
	public function checkingWithF($f){
		if ($f == 0) {
			$this->BetReturnSteck();
		}
		if ($f == -0.25) {
			$this->BetHalfLoss();
		}
		if ($f == 0.25) {
			$this->BetHalfWin();
		}
		if ($f < -0.25) {
			$this->BetLoss();
		}
		if ($f > 0.25) {
			$this->BetWin();
		}
	}
}