<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 *  This will contain all functions which are require to load matches for all sports.
 *
 *  @author Anirban Saha
 */
class MatchListing extends FrontendBaseController
{
    public function postShowMatches(Request $request, $sport_slug)
    {
		if(count($request->leagues)){
			$time_range				=	$request->time_range ? (int)$request->time_range : null;
			$league_ids				=	$request->leagues;
			$data					=	$this->getMatchesListData($sport_slug, $league_ids, $time_range);
			// $data['bookmaker_id']	=	$this->getBookmaker();
			// $data['banner_images']	=	$this->getBannerImages();
			$data['menu'] = "Pre-Match-Betting";
			$data['active_sport'] = $sport_slug;
			return view('frontend.matches.index', $data);
		} else {
			$this->setFlashAlert('danger', __('alert_info. No leagues selected'));
			return back();
		}
    }
}
