<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Traits\Listing\Odds;
/**
 *  This will contain all functions which are require to load extra odds for all sports.
 *
 *  @author Anirban Saha
 */

class OddsListing extends FrontendBaseController
{
	use Odds;

	/**
	 * fetches all the markets and odds for a match
	 * @param  Request 		$request  		sports id and match id
	 * @return html           list of the markets and the odds
	 */
    public function fetchExtraOdds(Request $request)
    {
		$this->getExtraOdds($request->sport_id, $request->match_id);
		if ($request->match_id && $request->sport_id) {
			$data	=	$this->getExtraOdds($request->sport_id, $request->match_id);
		} else {
			//invalid request
			$data					=	[];
			$data['extra_odds']		=	[];
			$data['market_details']	=	[];
		}
		// a($data['extra_odds']);
		return view('frontend.odds.extra_odds_listing', $data);
    }
}
