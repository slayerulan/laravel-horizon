<?php
namespace App\Http\Controllers\BetCalculations;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Traits\BetCalculations\CalculationFormula;
use DB;
use App\FeedModel\Sport;
use App\Betslip;
use App\ComboBetslip;
use App\ComboBetslipCombination;
use App\UniqueCalculation;
use App\UserWallet;
use App\Transaction;
use App\UsersActivityLog;
use App\PendingCancelMatche;

/**
 * This will calculate all markets for All Sports
 *
 * @author Arijit Jana
 */
class LiveBetCalculation extends BetCalculation
{
	/**
	 * Calculates live single bet of all sports.
	 *
	 * @param      string      $game       Name of the game, which is been calculated.
	 */
    public function singleBetCalculation($game){
		echo date('Y-m-d H:i:s')."<pre>";
		$sport = Sport::where('name', $game)->first();
		$all_bets = Betslip::where('status', 'pending')->where('sport_id', $sport->id)->where('bet_type', 'live')->get();
		
		// we have some not calculated bet
		if ($all_bets) {
            $this->bet_type = 'single';
            foreach($all_bets as $each_bet){
                $this->calculate_bet   		= false;
				$this->single_bet_info 		= $each_bet;
				$this->user_id 				= $each_bet->user_id;
                $this->no_data_found 		= true;
                $match_status 				= $this->checkMatchStatus();
				
                if($this->calculate_bet == true){
					// everything is correct, we can calculate
					
                    $this->CalculateEachBet();
                    $this->saveSingleBetCalculation($each_bet->user_id, $this->return_amount, $each_bet->id, $this->win_or_loss, $this->bet_status);
                }
            }
        }
    }
	
	/**
	 * Saves the calculated single bet.
	 *
	 * @param      integer      $user_id         Id of the user who placed this bet.
	 * @param      integer      $win_amount      Amount that user won for this bet.
	 * @param      integer      $row_id          Id from the betslips table.
	 * @param      integer      $status          Status of calculated bet. 0 = lost / 1 = win.
	 * @param      string       $bet_status      Status that will be displayed to the.
	 */
	public function saveSingleBetCalculation($user_id, $win_amount, $row_id, $status, $bet_status = null) {
        switch ($status) {
            case 0:
                Betslip::where('id', $row_id)->update(['status' => 'lost']);
                break;
            case 1:
                $ref_no   	  = $this->single_bet_info->bet_number;
				$title	  	  = "Win Prize of Live-Match Single Bet";
				$table 	  	  = 'betslips';
				$currency 	  = $this->single_bet_info->currency;
				$win_amount   = $this->checkIfMaxPayoutLimitCrossed($win_amount, $currency, 'single');
				$update_array = ['status' => $bet_status,'prize_amount' => $win_amount];
				$this->payToUserPrizeAccount($user_id,$ref_no,$win_amount,$row_id,$update_array,$table,$title);
                break;
        }
    }

	/**
	 * Calculates combo bet for all sports.
	 *
	 * @param      string     $game      Name of the game, which is been calculated
	 */
    public function comboBetCalculation($game){
		echo date('Y-m-d H:i:s')."<pre>";
		$sport = Sport::where('name', $game)->first();
		$all_bets = ComboBetslip::where('status', 'pending')->where('sport_id', $sport->id)->get();

		// we have some not calculated bet
		if ($all_bets) {
			$this->bet_type = 'combo';
			$result_status_array = ['pending'=>3, 'win'=>1, 'lost'=>2, 'half_lost'=>4, 'half_win'=>4, 'return_stake'=>5, 'cancel'=>0];
			foreach($all_bets as $each_combination){
                $this->result_status       = [];
				$this->combo_bet_info	   = $each_combination;
				$this->user_id			   = $each_combination->user_id;
                $combination = ComboBetslipCombination::where('combo_betslip_id', $each_combination->id)->get();
				
				// set required varriable null at the start of a combo
				if ($combination) {
                    $this->cancel_match_count  = 0;
                    $this->total_number_of_bet = count($combination);
                    foreach ($combination as $each_bet) {
						if($each_bet->status == 'pending') {
							$this->single_bet_info = $each_bet;
							// set each bet not calculated at first
							$this->result_status['' . $this->single_bet_info->match_id . '@' . $this->single_bet_info->market_name . ''] = 3;
							
							// calculate only live matches
							if($each_bet->bet_type == 'live') {
								$this->calculate_bet 	= false;
								$this->no_data_found 	= true;
								$this->match_info     	= false;
								$match_status 			= $this->checkMatchStatus();
								// everything is correct, we can calculate
								if($this->calculate_bet == true){
									$this->CalculateEachBet();
								}
							}
						}
						else{
							$this->result_status['' . $each_bet->match_id . '@' . $each_bet->market_name . ''] = $result_status_array[$each_bet->status];
							if($each_bet->status == 'cancel') {
								$this->cancel_match_count++;
							}
						}
					}
				}
				// after end of each bet of a combo calculate the total combo
				$this->calculateEachCombo();
			}
		}
	}
}