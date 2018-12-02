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
use App\BetAmountReturn;
use App\Http\Traits\Api\ProviderApiTrait;
use App\User;
use App\BetRule;
use App\Currency;
use App\ConfluxUser;

/**
 * This will calculate all markets for All Sports
 *
 * @author Arijit Jana
 */
class BetCalculation extends Controller
{
    use CalculationFormula, ProviderApiTrait;
	
	/**
	 * This will contain bet type.possible values('single','combo')
	 */
	public $bet_type				= false;
	public $calculate_bet			= false;
	public $single_bet_info			= false;
	public $combo_bet_info			= false;
	public $user_id					= false;
	public $no_data_found			= true;
	public $bet_not_calculate		= false;
	public $cancelled_match_status 	= ['256', '257', '355', '356', '357', '260'];
	public $win_or_loss 			= 0;
	// ['pending'=>3, 'win'=>5, 'lost'=>6, 'half_lost'=>8, 'half_win'=>10, 'return_stake'=>9, 'cancel'=>15];
	public $bet_status 				= false;
	public $total_number_of_bet		= 0;
	public $cancel_match_count		= 0;
	public $result_status     		= false;
	public $match_info     			= false;
	public $return_amount     		= false;

	/**
	 * Calculates single bet of all sports.
	 *
	 * @param      string      $game       Name of the game, which is been calculated.
	 */
	public function singleBetCalculation($game) {
		echo date('Y-m-d H:i:s')."<pre>";
		$sport = Sport::where('name', $game)->first();
		$all_bets = Betslip::where('status', 'pending')->where('sport_id', $sport->id)->where('bet_type', 'pre')->get();

		// we have some not calculated bet
		if ($all_bets) {
			$this->bet_type = 'single';
			foreach($all_bets as $each_bet) {
				$this->calculate_bet 		= false;
				$this->single_bet_info 		= $each_bet;
				$this->user_id 				= $each_bet->user_id;
				$this->no_data_found 		= true;
				$this->bet_not_calculate 	= false;
				$this->match_info     		= false;
				$match_status 				= $this->checkMatchStatus();

				if($this->calculate_bet == true){
					// everything is correct, we can calculate
					$this->CalculateEachBet();
					if($this->bet_not_calculate == false){
						$this->saveSingleBetCalculation($each_bet->user_id, $this->return_amount, $each_bet->id, $this->win_or_loss, $this->bet_status);
					}
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
                // $this->db->where('id',(int)$row_id)->update(BET_PLACE_HISTORY,['status' => 6]);
				Betslip::where('id', $row_id)->update(['status' => 'lost']);
                break;
            case 1:
                $ref_no   		= $this->single_bet_info->bet_number;
				$title	  		= "Win Prize of Pre-Match Single Bet";
				$table 	  		= 'betslips';
				$currency 		= $this->single_bet_info->currency;
				$win_amount 	= $this->checkIfMaxPayoutLimitCrossed($win_amount, $currency, 'single');
				$update_array 	= ['status' => $bet_status,'prize_amount' => $win_amount];
				$this->payToUserPrizeAccount($user_id,$ref_no,$win_amount,$currency,$row_id,$update_array,$table,$title);
                break;
        }
        // if($this->single_bet_info->sport_id == 50){
            // $this->updateBonusHistory($user_id, $win_amount, $row_id, $status, $bet_status);
        // }
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
							
							// calculate only pre matches
							if($each_bet->bet_type == 'pre') {
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
	
	/**
	 * Calculates the each combo after calculating each bet of a combination.
	 */
	public function calculateEachCombo(){
		if(count($this->result_status) > 0){
			// we have some calculated bet
			if (in_array(2, $this->result_status)) {
				// bet failed, no money
				$this->saveComboBetCalculation(0);
			}
			else {
				if (in_array(3, $this->result_status)) {
					// bet in progress
				}
				else if ((int)$this->cancel_match_count == (int)$this->total_number_of_bet) {
					// all match cancelled, should return steck
					$this->returnStackDueToCancelMatch('refund');
				}
				else {
					// all combination win, return win prize
					$this->saveComboBetCalculation(1);
				}
			}
		}
	}
	
	/**
	 * Saves a calculated combo bet.
	 *
	 * @param      integer      $status      Status of calculated bet. 0 = lost / 1 = win
	 */
	public function saveComboBetCalculation($status) {
		$ref_no 	= $this->combo_bet_info->bet_number;
		$row_id 	= (int)$this->combo_bet_info->id;
		$user_id 	= (int)$this->user_id;
        switch ($status) {
            case 0:
                ComboBetslip::where('id', $row_id)->update(['status' => 'lost']);
                break;
            case 1:
				$title	  		= "Win Prize for Combo Bet";
				$bet_status 	= 'win';
				$table 			= 'combo_betslips';
				$all_bet 		= ComboBetslipCombination::where('combo_betslip_id', $row_id)->get();
				if ($all_bet) {
					$odds_value_count = 1;
					foreach ($all_bet as $i => $combo_details) {
						$odds_value_count = $odds_value_count * $combo_details->calculated_odds_value;
					}
				}
				$odds_value_count = $odds_value_count > 100 ? 100 : $odds_value_count;
				$check_amount     = $this->combo_bet_info->stake_amount;
				$currency		  = $this->combo_bet_info->currency;
				$win_amount       = floor($check_amount * $odds_value_count);
				$win_amount 	  = $this->checkIfMaxPayoutLimitCrossed($win_amount, $currency, 'combo');
				$update_array 	  = ['status' => $bet_status,'prize_amount' => $win_amount];
				$this->payToUserPrizeAccount($user_id,$ref_no,$win_amount,$currency,$row_id,$update_array,$table,$title);
                break;
        }
    }

    /**
     * Checks if the max payout limit for a bet is reached and if does returns the max payout value.
     *
     * @param      integer      $win_amount      Product of odd value and stake amount for a bet.
     * @param      string       $currency        Currency that the user use for money transaction.
     * @param      string       $bet_type        single / combo.
     *
     * @return     integer  ( Amount that the user will get credited. )
     */
    public function checkIfMaxPayoutLimitCrossed($win_amount, $currency, $bet_type) {
    	$user = User::find($this->user_id);
		if ($user->bet_rule_id != NULL) {
			$bet_rule = BetRule::find($user->bet_rule_id);
		}
		else{
			$bet_rule = BetRule::where('is_default', 'yes')->first();
		}
		$rule = json_decode($bet_rule->rule);

		if ($bet_type == 'single') {
			if (isset($rule->maximum_straight_bet_payout->{$currency}) && $win_amount > $rule->maximum_straight_bet_payout->{$currency}) {
				$win_amount = $rule->maximum_straight_bet_payout->{$currency};
			}
		}
		else{
			if (isset($rule->maximum_parlay_payout->{$currency}) && $win_amount > $rule->maximum_parlay_payout->{$currency}) {
				$win_amount = $rule->maximum_parlay_payout->{$currency};
			}
		}
		return $win_amount;
    }

	/**
	 * Distributes prize and updates all details.
	 *
	 * @param      integer     $user_id           Id of the user who placed this bet.
	 * @param      string      $ref_no            Unique number for a particular bet
	 * @param      integer     $win_amount        Amount that user won for this bet.
	 * @param      string      $currency          Currency for the user.
	 * @param      integer     $row_id            Id from the betslips table.
	 * @param      array       $update_array      Array containing the data that neet to be updated in betslips or combo_betslips.
	 * @param      string      $table             If calculated bet is single = betslips, else combo_betslips.
	 * @param      string      $title             The title
	 */
	public function payToUserPrizeAccount($user_id,$ref_no,$win_amount,$currency,$row_id,$update_array,$table,$title) {
		$error = array();
		$win_amount = floor($win_amount);
		$unique_calculation = UniqueCalculation::where('bet_number', $ref_no)->first();
		
		if(empty($unique_calculation)) {

			$player_id = ConfluxUser::where('user_id', $user_id)->first()->conflux_user_id;
			DB::beginTransaction();
			try{
				UniqueCalculation::firstOrCreate(['bet_number' => $ref_no]);
				
				// $wallet_balance = UserWallet::where('user_id', $user_id)->first();
				
				// $updated_balance = $wallet_balance->amount + $win_amount;
				// $user_wallet = UserWallet::where('user_id', $user_id)->update(['amount' => $updated_balance]);

				$transaction = new Transaction;
				$transaction->user_id = $user_id;
				$transaction->type = 'credit';
				$transaction->title = ''.$this->bet_type.' Bet win ['.$ref_no.']';
				$transaction->amount = $win_amount;
				$transaction->status = 'queued';
				$transaction->save();

				$bet_amount_return = new BetAmountReturn;
				$bet_amount_return->bet_number = $ref_no;
				$bet_amount_return->player_id = $player_id;
				$bet_amount_return->transaction_id = $transaction->id;
				$bet_amount_return->return_type = 'win';
				$bet_amount_return->amount = $win_amount;
				$bet_amount_return->currency = $currency;
				$bet_amount_return->save();
				
				$activity_logs = new UsersActivityLog;
				$activity_logs->user_id = $user_id;
				$activity_logs->event = ''.$win_amount.' Win for Bet number '.$ref_no.'';
				$activity_logs->save();
				
				// updating betslip table
				DB::table($table)->where('id', $row_id)->update($update_array);
				
				DB::commit();
			}
			catch (\Exception $e) {
				DB::rollback();
			}
		}
		else{
			$activity_logs = new UsersActivityLog;
			$activity_logs->user_id = $user_id;
			$activity_logs->event = ''.$win_amount.' Win for Bet number '.$ref_no.'';
			$activity_logs->save();
		}
	}
	
	// this will check if we get a cancel status for a match
	public function checkCancelMatchStatus() {
		// $check_for_track = getSelectedValue('updated_at',PENDING_CANCEL_MATCH_RECORD,'match_id ="'.$this->single_bet_info->match_id.'" AND sports_type LIKE "'.$this->single_bet_info->sport_type.'"');
		$check_for_track = PendingCancelMatche::where('match_id', $this->single_bet_info->match_id)->first();
		
		// already inserted need to check time
		if(!empty($check_for_track)){
			// 3 hours past, should cancel and return stack
			if(date('Y-m-d H:i:s',strtotime($check_for_track->updated_at)) <= date('Y-m-d H:i:s',strtotime('- 180 minutes'))){
				// $this->match_info->bet_cancel_status == "cancel";
				Betslip::where('id', $this->single_bet_info->id)->update(['status' => 'cancel']);
				$this->checkAndCancelMatch();
			}
			else{
				// restrict from calculation
				$this->calculate_bet = false;
			}
		}
		else{
			// not exist,should insert
			$this->calculate_bet = false;
			
			// $insert_data = ['sports_type' => $this->single_bet_info->sport_type , 'match_id' => $this->single_bet_info->match_id , 'status' => $this->match_info->match_status ,'updated_at' => date('Y-m-d H:i:s')];
			// $this->db->insert(PENDING_CANCEL_MATCH_RECORD,$insert_data);
			
			$pending_cancel_matche = new PendingCancelMatche;
			$pending_cancel_matche->match_id = $this->single_bet_info->match_id;
			$pending_cancel_matche->sports_id = $this->single_bet_info->sport_id;
			$pending_cancel_matche->status = $this->single_bet_info->status;
			$pending_cancel_matche->save();
		}
	}
	
	// this will check match details and cancel the bet
	public function checkAndCancelMatch() {
		$this->calculate_bet = false;
		if($this->match_info != false && $this->no_data_found == false && is_object($this->match_info) && (in_array($this->match_info->period,$this->cancelled_match_status) || $this->match_info->status != "active")) {
			if($this->bet_type == "single"){
				// match cancelled should return stack
				$this->returnStackDueToCancelMatch('cancel');
			}
			else{
				// match cancelled we should change odd value for combo
				$this->cancel_match_count++;
				$this->result_status['' . $this->single_bet_info->match_id . '@' . $this->single_bet_info->odds_type . ''] = 11;
				$this->changeOddValue(1,'cancel');
			}
		}
	}
	
	// this will change odd value for combo bet
	public function changeOddValue($odd_value,$status) {
		$data = [];
		$data['calculated_odds_value'] = $odd_value;
		$data['status'] = $status;
		ComboBetslipCombination::where('id', $this->single_bet_info->id)->update($data);
	}

	/**
	 * Returns the bet placed amount.
	 *
	 * @param      string    $return_type    refund='bet nither won nor lost' / cancel='match or bet canceled'
	 */
	public function returnStackDueToCancelMatch($return_type = 'refund') {
		$user_id = $this->user_id;
		$user_wallets = UserWallet::where('user_id', $user_id)->first();
		if($this->bet_type == "single"){
			$amount 			= 	$this->single_bet_info->stake_amount;
			$currency 			= 	$this->single_bet_info->currency;
			$updated_balance	= 	$user_wallets->amount + $amount;
			$bet_table 			= 	'betslips';
			$table_id 			= 	(int)$this->single_bet_info->id;
			$update_data 		= 	['status' => 'cancel'];
			$account_title 		= 	"Return stack because of cancelling match";
			$ref_no 			= 	$this->single_bet_info->bet_number;
		}
		else if($this->bet_type == "combo"){
			$amount 			= 	$this->combo_bet_info->stake_amount;
			$currency 			= 	$this->combo_bet_info->currency;
			$updated_balance 	= 	$user_wallets->amount + $amount;
			$bet_table 			= 	'combo_betslips';
			$table_id 			= 	(int)$this->combo_bet_info->id;
			$update_data 		= 	['status' => 'cancel'];
			$account_title 		= 	"Return stack because of cancelling all matches of combo";
			$ref_no 			= 	$this->combo_bet_info->combination_id;
		}
		else{
			// this is for toto
			$amount 			= 	$this->single_bet_info->form_price;
			$currency 			= 	$this->single_bet_info->currency;
			$updated_balance 	= 	$user_wallets->amount + $amount;
			$bet_table 			= 	TOTO_USER_FORM_DETAILS;
			$table_id 			= 	(int)$this->single_bet_info->id;
			$update_data 		= 	['status' => 'cancel'];
			$account_title 		= 	"Return stack because of cancelling Toto Form";
			$ref_no 			= 	$this->single_bet_info->unique_form_id;
		}

		$player_id = ConfluxUser::where('user_id', $user_id)->first()->conflux_user_id;
		
		DB::beginTransaction();
		try{
			UniqueCalculation::firstOrCreate(['bet_number' => $ref_no]);

			// UserWallet::where('user_id', $user_id)->update(['amount' => $updated_balance]);

			$transaction = new Transaction;
			$transaction->user_wallet_id = $user_wallets->id;
			$transaction->type = 'credit';
			$transaction->title = ''.$this->bet_type.' Bet return stack ['.$ref_no.']';
			$transaction->amount = $amount;
			$transaction->status = 'queued';
			$transaction->save();

			$bet_amount_return = new BetAmountReturn;
			$bet_amount_return->bet_number = $ref_no;
			$bet_amount_return->player_id = $player_id;
			$bet_amount_return->transaction_id = $transaction->id;
			$bet_amount_return->return_type = $return_type;
			$bet_amount_return->amount = $amount;
			$bet_amount_return->currency = $currency;
			$bet_amount_return->save();
			
			$activity_logs = new UsersActivityLog;
			$activity_logs->user_id = $user_id;
			$activity_logs->event = ''.$amount.' return stack for Bet number '.$ref_no.'';
			$activity_logs->save();
			
			// updating betslip table
			DB::table($bet_table)->where('id', $table_id)->update($update_data);
			
			DB::commit();
		}
		catch (\Exception $e) {
			DB::rollback();
		}
	}
}