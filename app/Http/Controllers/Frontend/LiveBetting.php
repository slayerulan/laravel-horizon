<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Traits\PrematchBetting\PlaceBet;
use App\Http\Traits\PrematchBetting\SaveBet;

/**
 *  this will help to place live match bet.
 *
 *  @author Arijit Jana
 */
class LiveBetting extends FrontendBaseController
{
	use PlaceBet;

	/**
	 *  this will save a live bet into session and generate updated betslip
	 *
	 *  @param  Request  $request   	post request
	 *
	 *  @return  html   load updated betslip
	 */
    public function placeLiveBetIntoSlip(Request $request)
    {
    	$country		=	$request->country;
    	$league			=	$request->league;
    	$match_id		=	$request->match_id;
    	$market_id		=	$request->market_id;
    	$bet_for		=	$request->bet_for;
		if($this->isValidLiveOdd($country, $league, $match_id, $market_id, $bet_for)) {
			$this->setLiveBetIntoSession();
		}
		return view('frontend.betSlip.slip_body');
    }
}
