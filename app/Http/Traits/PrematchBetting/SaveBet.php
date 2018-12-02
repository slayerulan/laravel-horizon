<?php

namespace App\Http\Traits\PrematchBetting;

use DB;
use App\Betslip;
use App\ComboBetslip;
use App\Transaction;
use Illuminate\Support\Facades\Session;

/**
 * This will contain all functions that will help to save a pre match bet
 *
 *  @author Anirban Saha
 */
trait SaveBet
{
	/**
	 *  this will contain each single bet details
	 *
	 *  @var  array
	 */
	public 	$each_bet	=	[];

	/**
	 *  this will contain combination of each combo bet
	 *
	 *  @var  array
	 */
	public 	$combination	=	[];

	/**
	 *  this will contain bet stake amount
	 *
	 *  @var  int
	 */
	public 	$total_amount		=	0;

	/**
	 *  this will contain error bet count set from saveEachSingleBet
	 *
	 *  @var  int
	 */
	public	$error				=	0;

	/**
	 *  this will contain the counts of single bets that is successfully placed
	 *
	 *  @var  int
	 */
	public	$placed_bet_count	=	0;

	/**
	 *  this will contain error message set from saveEachSingleBet
	 *
	 *  @var  bool|string
	 */
	public	$error_message		=	false;

	/**
	 *  this will save all single bet into database
	 *
	 *  @param  array  $betwise_stake  odds id in key and stake amount in value
	 *
	 *   @return  array  status && message
	 */
	public function saveSingleBetIntoDb($betwise_stake)
	{
		foreach ($betwise_stake as $key => $amount) {
			$this->setEachSingleBet($key, $amount)->saveEachSingleBet();
		}
		return $data	= 	$this->getAlertData(count($betwise_stake));
	}

	public function saveComboBetIntoDb($bet_stake)
	{
		$this->total_amount = $bet_stake;
		$this->setComboBet();

		$data			=	[];
		$data['status'] = 	'Error';

		$ticketId = $this->each_bet['bet_number'];
		$amount = $bet_stake;
		$bet_info = 'Combo bet';
		$bet_request = $this->sendBetRequest($ticketId, $amount, $bet_info);
		Session::put('user_details.balance',$bet_request->balance);
		// if($this->user->getWallet() && $this->user->getWallet()->amount >= $this->total_amount) {
		if($bet_request->status == 1) {
			//enough account balance
			
			// DB::beginTransaction();
			// try {
				// $current_balance	=	$this->user->getWallet()->amount;
				$combo_bet	=	ComboBetslip::create($this->each_bet);
				foreach ($this->combination as  $each_combination) {
					$combo_bet->combinations()->create($each_combination);
				}
				// $transaction	=	[
				// 	'type'		=> 'debit',
				// 	'title'		=> 'Combo Bet Placed ['.$this->each_bet['bet_number'].']',
				// 	'amount'	=> $this->total_amount,
				// ];
				// $this->user->getWallet()->transaction()->create($transaction);
				// $current_balance	=	($current_balance - $this->total_amount);
				// $this->user->getWallet()->update(['amount' => $current_balance]);
				
				$transaction 			= new Transaction;
				$transaction->user_id 	= $this->user_id;
				$transaction->type 		= 'debit';
				$transaction->title 	= 'Combo Bet Placed ['.$this->each_bet['bet_number'].']';
				$transaction->amount 	= $this->total_amount;
				$transaction->status 	= 'debited';
				$transaction->save();

				$this->log('Combo Bet Placed');
				// DB::commit();
				$this->unsetAllBets();
				// $this->setUserDetailsIntoSession();
				$data['status'] 	= 'Success';
				$success_message	= __('alert_info. Bet Placed!');
				if ($this->odds_value_changed == true) {
					$success_message.=	__('alert_info. Some of the odds value have changed.');
				}
				$data['message']	= $success_message;
				$this->odds_value_changed = false;
			// } catch (\Exception $e) {
			// 	DB::rollback();
			// 	$data['message']	=	__('alert_info. due to some error!');
			// }
		}
		else {
			$data['message']	=	$bet_request->message;
			// $data['message']	=	__('alert_info. due to insufficient balance!');
		}
		return $data;
	}

	/**
	 *  this will set each single bet details into variable
	 *
	 *  @param  int  $odd_id  odds id
	 *  @param  int  $amount  amount
	 *
	 *  @return self
	 */
	public function setEachSingleBet($odd_id, $amount)
	{
			$bet_details				=	session('pre_match_selected_bet')[$odd_id];
			$each_bet					=	[];
			$each_bet['bet_number']		=	'111'.mt_rand(99,999).uniqid();
			$each_bet['user_id']		=	$this->user_id;
			$each_bet['sport_id']		=	$bet_details['sport_id'];
			$each_bet['match_id']		=	$bet_details['match_id'];
			$each_bet['market_name']	=	$bet_details['market_name'];
			$each_bet['bet_for']		=	$bet_details['bet_for'];
			$each_bet['extra_value']	=	$bet_details['extra_value'];
			$each_bet['odds_value']		=	$bet_details['odds_value'];
			$each_bet['bet_type']		=	$bet_details['type'];
			$each_bet['stake_amount']	=	$amount;
			$each_bet['currency']		=	Session::get('conf_user_details')->currency;
			$each_bet['prize_amount']	=	0;
			$this->each_bet				=	$each_bet;
			$this->total_amount			=	$amount;
			return $this;
	}

	/**
	 *  this will save each bet into databse and deduct amount from wallet
	 */
	public function saveEachSingleBet()
	{
		$ticketId = $this->each_bet['bet_number'];
		$amount = $this->each_bet['stake_amount'];
		$bet_info = [
			'market_name' 	=> $this->each_bet['market_name'],
			'bet_for' 		=> $this->each_bet['bet_for'],
			'odds_value' 	=> $this->each_bet['odds_value'],
			'bet_type'		=> $this->each_bet['bet_type'],
		];
		$bet_request = $this->sendBetRequest($ticketId, $amount, json_encode($bet_info));
		Session::put('user_details.balance',$bet_request->balance);
		// if($this->user->getWallet() && $this->user->getWallet()->amount >= $this->total_amount) {
		if($bet_request->status == 1) {
			//enough account balance
			// $current_balance	=	$this->user->getWallet()->amount;
			// DB::beginTransaction();
			// try {
				Betslip::create($this->each_bet);
				// $this->user->getWallet()->transaction()->create($transaction);
				// $current_balance	=	($current_balance - $this->total_amount);
				// $this->user->getWallet()->update(['amount' => $current_balance]);
				
				$transaction 			= new Transaction;
				$transaction->user_id 	= $this->user_id;
				$transaction->type 		= 'debit';
				$transaction->title 	= 'Single Bet Placed ['.$this->each_bet['bet_number'].']';
				$transaction->amount 	= $this->total_amount;
				$transaction->status 	= 'debited';
				$transaction->save();

				$this->log('Single Bet Placed');
			// 	DB::commit();
			// } catch (\Exception $e) {
			// 	DB::rollback();
			// 	$this->error++;
			// 	$this->error_message	=	__('alert_info. due to some error!');
			// }
			$this->placed_bet_count++;
		}
		else {
			$this->error++;
			// $this->error_message	=	__('alert_info. due to insufficient balance!');
			$this->error_message	=	$bet_request->message;
		}
	}

	public function setComboBet()
	{
		$combination			=	[];
		foreach (session('pre_match_selected_bet') as $odd_id => $each_bet) {
			$sport_id							=	$each_bet['sport_id'];
			$each_combination					=	[];
			$each_combination['sport_id']		=	$each_bet['sport_id'];
			$each_combination['match_id']		=	$each_bet['match_id'];
			$each_combination['market_name']	=	$each_bet['market_name'];
			$each_combination['bet_for']		=	$each_bet['bet_for'];
			$each_combination['extra_value']	=	$each_bet['extra_value'];
			$each_combination['odds_value']		=	$each_bet['odds_value'];
			$each_combination['bet_type']		=	$each_bet['type'];
			$combination[]						=	$each_combination;
		}
		$sport_id	=	count(array_unique(array_pluck(session('pre_match_selected_bet'),'sport_id'))) > 1 ? 0 : $sport_id;
		$combo	=	[];
		$combo['bet_number']	=	'222'.mt_rand(99,999).uniqid();
		$combo['user_id']		=	$this->user_id;
		$combo['sport_id']		=	$sport_id;
		$combo['stake_amount']	=	$this->total_amount;
		$combo['currency']		=	Session::get('conf_user_details')->currency;
		$this->each_bet			=	$combo;
		$this->combination		=	$combination;

	}
	/**
	 *  this will return alert data
	 *
	 *  @param  int  $total_bet  count of total bet
	 *  @return  array  status && message
	 */
	public function getAlertData($total_bet)
	{
		$data 				= 	[];
		$data['status']		=	$this->error == 0 ? 'Success' : ($this->error == $total_bet ? 'Error' : 'Info');
		$success_message	=	$this->placed_bet_count.' '. __('alert_info.'. str_plural('Bet',$this->placed_bet_count) .' Placed Successfully.');
		if ($this->odds_value_changed == true) {
			$success_message .=	__('alert_info. Some of the odds value have changed.');
		}
		$error_message		=	$this->error.' '. __('alert_info.'. str_plural('bet',$this->error) .' could not be placed. '.$this->error_message);
		$data['message']	=	$data['status'] == 'Success' ? $success_message :
								($data['status'] == 'Error' ? $error_message : $success_message .', '.$error_message);
		if ($data['status'] == 'Success') {
			$this->unsetAllBets();
			// $this->setUserDetailsIntoSession();
		}
		$this->odds_value_changed = false;
		return $data;
	}
}
