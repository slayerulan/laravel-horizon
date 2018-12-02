<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Traits\PrematchBetting\PlaceBet;
use App\Http\Traits\PrematchBetting\SaveBet;
use App\Http\Traits\Api\ProviderApiTrait;
use Illuminate\Support\Facades\Session;

/**
 *  this will help to place pre match bet.
 *
 *  @author Anirban Saha
 */
class PrematchBetting extends FrontendBaseController
{
	use PlaceBet, SaveBet, ProviderApiTrait;

	/**
	 * indicates if any odds value have changed
	 *
	 * @var        boolean
	 */
	public $odds_value_changed = false;

	/**
	 *  this will save a bet into session and generate updated betslip
	 *
	 *  @param  Request  $request  request
	 *
	 *  @return  string   load updated betslip
	 */
    public function placeBetIntoSlip(Request $request)
    {
    	$odds_id		=	$request->odds_id;
		if($this->isValidOdd($odds_id)) {
			$this->setBetIntoSession();
		}
		return view('frontend.betSlip.slip_body');
    }

	/**
	 *  this will unset a bet from session and generate updated betslip
	 *
	 *  @param  Request  $request  request
	 *
	 *  @return  string   0 for error, else updated betslip
	 */
	public function removeBetFronSlip(Request $request)
	{
		if(session('pre_match_selected_bet')[$request->odd_id]) {
			$request->session()->forget('pre_match_selected_bet.'.$request->odd_id);
			$request->session()->forget('unique_bet_key.'.$request->odd_id);
			return view('frontend.betSlip.slip_body');
		}
		echo "0";
	}

	/**
	 *  this will load betslip
	 *
	 *  @return  string  betslip
	 */
	public function getBetSlip()
	{
		return view('frontend.betSlip.slip_body');
	}

	/**
	 *  this will save all single bet from betSlip
	 *
	 *  @param  Request  $request  request object with stake_amount array
	 *
	 *  @return  string   json_encoded response with status and message
	 */
	public function saveSingleBet(Request $request)
	{
		$data 			= [];
		$data['status']	= 'Error';
		if($this->user_id) {
			$stake_array	= 	$request->stake_amount;
			$odds_array		= 	$request->odds_values;
			$count 			=	0;
			$betwise_stake	=	[];
			$error			=	0;
			$currency		= Session::get('conf_user_details')->currency;
			foreach (session('pre_match_selected_bet') as $odd_id => $each_bet) {
				if(isset(getBetRule()->straight_bet_min_limit->{$currency}) && abs($stake_array[$count]) < getBetRule()->straight_bet_min_limit->{$currency}) {
					$data['message']	= __('alert_info. Minimum Stake Limit is ').getBetRule()->straight_bet_min_limit->{$currency};
					$error++;
					break;
				}
				elseif (isset(getBetRule()->straight_bet_max_limit->{$currency}) && abs($stake_array[$count]) > getBetRule()->straight_bet_max_limit->{$currency}) {
					$data['message']	= __('alert_info. Maximum Stake Limit is ').getBetRule()->straight_bet_max_limit->{$currency};
					$error++;
					break;
				}
				else {

					if ($each_bet['type'] == 'live') {
						if($this->isValidLiveOdd($each_bet['country'], $each_bet['league'], $each_bet['match_id'], $each_bet['market_id'], $each_bet['bet_for'])) {
							if ($odds_array[$count] != round($this->live_odd_details['odds_value'], 2)) {
								$this->odds_value_changed = true;
							}
							if ($request->except_single_odds_changes != 1 && $this->odds_value_changed == true) {
								$data['message']	= __('alert_info. You need to agree to except all odds changes to place this bet');
								$error++;
								break;
							}
							else{
								$this->setLiveBetIntoSession();
								$betwise_stake[$odd_id]	=	ceil(abs($stake_array[$count]));
							}
						}
					}
					else{
						if($this->isValidOdd($odd_id)) {
							if ($odds_array[$count] != round($this->odd_details->odds_value, 2)) {
								$this->odds_value_changed = true;
							}
							if ($request->except_single_odds_changes != 1 && $this->odds_value_changed == true) {
								$data['message']	= __('alert_info. You need to agree to except all odds changes to place this bet');
								$error++;
								break;
							}
							else{
								$this->setBetIntoSession();
								$betwise_stake[$odd_id]	=	ceil(abs($stake_array[$count]));
							}
						}
					}
					
				}
				$count++;
			}
			if($error == 0) {
				$data	=	$this->saveSingleBetIntoDb($betwise_stake);
			}
		} else {
			$data['message']	= __('alert_info. Please Login First!');
		}
		echo json_encode($data);
	}

	/**
	 *  this will save all combo bet from betSlip
	 *
	 *  @param  Request  	$request  		request object with stake_amount array
	 *
	 *  @return  string   		json_encoded response with status and message
	 */
	public function saveComboBet(Request $request)
	{
		$odds_array		= $request->odds_values;
		$data 			= [];
		$data['status']	= 'Error';
		$count 			= 0;
		$error			= 0;
		$currency		= Session::get('conf_user_details')->currency;
		if($this->user_id) {
			$stake_amount	= 	abs($request->stake_amount);
			$total_bet	= 	abs($request->bet);
			if($total_bet > getBetRule()->maximum_number_of_bets_per_parlay) {
				$data['message']	= __('alert_info. Maximum Combo Bet Limit exceed.');
			}
			elseif ($total_bet < getBetRule()->minimum_number_of_bets_per_parlay) {
				$data['message']	= __('alert_info. Minimum Combo Bet Limit not reached.');
			}
			else {

				if(isset(getBetRule()->parlay_min_limit->{$currency}) && $stake_amount < getBetRule()->parlay_min_limit->{$currency}) {
					$data['message']	= __('alert_info. Minimum Stake Limit is ').getBetRule()->parlay_min_limit->{$currency};
				}
				elseif (isset(getBetRule()->parlay_max_limit->{$currency}) && $stake_amount > getBetRule()->parlay_max_limit->{$currency}) {
					$data['message']	= __('alert_info. Maximum Stake Limit is ').getBetRule()->parlay_max_limit->{$currency};
				}
				else {
					
					$match_id_wise_max_count 	= max(array_count_values(array_pluck(session('pre_match_selected_bet'),'match_id')));
					if($match_id_wise_max_count == 1) {
						foreach (session('pre_match_selected_bet') as $odd_id => $each_bet) {

							if ($each_bet['type'] == 'live') {
								if($this->isValidLiveOdd($each_bet['country'], $each_bet['league'], $each_bet['match_id'], $each_bet['market_id'], $each_bet['bet_for'])) {
									if ($odds_array[$count] != round($this->live_odd_details['odds_value'], 2)) {
										$this->odds_value_changed = true;
									}
									if ($request->except_combo_odds_changes != 1 && $this->odds_value_changed == true) {
										$data['message']	= __('alert_info. You need to agree to except all odds changes to place this bet');
										$error++;
										break;
									}
									else{
										$this->setLiveBetIntoSession();
									}
								}
							}
							else{
								if($this->isValidOdd($odd_id)) {
									if ($odds_array[$count] != round($this->odd_details->odds_value, 2)) {
										$this->odds_value_changed = true;
									}
									if ($request->except_combo_odds_changes != 1 && $this->odds_value_changed == true) {
										$data['message']	= __('alert_info. You need to agree to except all odds changes to place this bet');
										$error++;
										break;
									}
									else{
										$this->setBetIntoSession();
									}
								}
							}
							$count++;
						}
						if($error == 0) {
							$data	=	$this->saveComboBetIntoDb($stake_amount);
						}
					}
					else {
						$data['message']	= __("alert_info. Two bets of same match can't be in a combo!");
					}
				}

			}
		}
		else {
			$data['message']	= __('alert_info. Please Login First!');
		}
		echo json_encode($data);
	}
}
