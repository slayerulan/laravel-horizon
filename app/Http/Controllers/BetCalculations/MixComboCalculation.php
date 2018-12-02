<?php

namespace App\Http\Controllers\BetCalculations;

use Illuminate\Http\Request;
use DB;
use App\FeedModel\Sport;
use App\ComboBetslip;
use App\ComboBetslipCombination;
/**
 * This will control the calculation of all markets for Mix combo bet
 *
 * @author Arijit Jana
 */
class MixComboCalculation extends BetCalculation
{	
	/**
	 * this will calculate combo bet for those where more than one game is selected
	 * @return string     time of the calculation
	 */
    public function index() {
		echo '<pre>'.date('Y-m-d H:i:s');
		$all_bets = ComboBetslip::where('status', 'pending')->where('sport_id', 0)->get();
		
		// we have some not calculated bet
		if($all_bets) {
			$this->bet_type = 'combo';
			foreach($all_bets as $each_combination){
                $this->result_status       = [];
				$this->combo_bet_info	   = $each_combination;
				$this->user_id			   = $each_combination->user_id;
				$combination = ComboBetslipCombination::where('combo_betslip_id', $each_combination->id)->get();
				
				// set required variable null at the start of a combo
				if ($combination) {
                    $this->cancel_match_count  = 0;
                    $this->total_number_of_bet = count($combination);
					// set each bet not calculated at first
                    foreach ($combination as $each_bet) {
						$sport_name = Sport::where('id', $each_bet->sport_id)->first()->name;
						$sport = str_replace(" ", "", $sport_name);
						$this->createProperty($sport, $sport.'Calculation');
						$this->$sport->single_bet_info = $each_bet;
						$this->$sport->calculate_bet   = false;
						$this->$sport->bet_type   = 'combo';
						$this->result_status['' . $each_bet->match_id . '@' . $each_bet->odds_type . ''] = 3;
						$match_status = $this->$sport->checkMatchStatus();
						// everything is correct, we can calculate
						if($this->$sport->calculate_bet == true){
							$this->$sport->CalculateEachBet();
							if($this->$sport->cancel_match_count){
								$this->cancel_match_count++;
							}
							$this->result_status = array_merge($this->result_status,$this->$sport->result_status);
						}
					}
				}
				// end of each combo
				$this->calculateEachCombo();
			}
		}
	}

	/**
	 * [createProperty description]
	 * @param  [type] $name  [description]
	 * @param  [type] $value [description]
	 * @return [type]        [description]
	 */
	public function createProperty($name, $value){
		$className = __NAMESPACE__ . '\\' . $value;
        $this->{$name} = new $className();
    }
}
