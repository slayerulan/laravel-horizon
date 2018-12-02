<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Country;

/**
 *  This will contain all functions which are require to load leagues for all sports.
 *
 *  @author Anirban Saha
 */
class LeagueListing extends FrontendBaseController
{
	/**
	 * Load league page for default sports.
	 *
	 *  @param   Request  $request  	request object
	 *  @return  string   	load landing page
	 */
	public function index()
    {
		$data	=	$this->getLeagueListData(config('bet_settings.default_sports_slug','football'));
		$data['menu'] = "Pre-Match-Betting";
		$data['active_sport'] = 'football';
		$data['sports_slide'] = $data['sports_bar'];
		return view('frontend.landing.index',$data);
    }
	/**
	 * Load league page for specific sports.
	 *
	 *  @param   Request  $request  request object
	 *  @return  string   load landing page
	 */
	public function getAllLeague(Request $request)
	{
		$data	=	$this->getLeagueListData($request->sport_slug);
		$data['menu'] = "Pre-Match-Betting";
		$data['active_sport'] = $request->sport_slug;

		foreach ($data['sports_bar'] as $key => $sport) {
			if ($sport->sport_slug == $data['active_sport']) {
				$index = $key;
				break;
			}
			else{
				$pre_active[] = $sport;
			}
		}
		if (isset($pre_active)) {
			$post_active = array_slice($data['sports_bar'],$index);
			$data['sports_slide'] = array_merge($post_active,$pre_active);
		}
		else{
			$data['sports_slide'] = $data['sports_bar'];
		}
		return view('frontend.landing.index',$data);
	}

	/**
	 *  Thiw will load leagues filtered by country and sports
	 *
	 *  @param   Request  $request  request object
	 *  @return  string   load landing page
	 */
	public function getLeagueByCountry(Request $request)
	{
		$country		=	Country::select('id')->where('name',$request->country_slug)->first();
		$country_id		=	isset($country->id) ? $country->id : null;
		$data			=	$this->getLeagueListData($request->sport_slug,$country_id);
		$data['menu'] = "Pre-Match-Betting";
		$data['active_sport'] = $request->sport_slug;

		foreach ($data['sports_bar'] as $key => $sport) {
			if ($sport->sport_slug == $data['active_sport']) {
				$index = $key;
				break;
			}
			else{
				$pre_active[] = $sport;
			}
		}
		if (isset($pre_active)) {
			$post_active = array_slice($data['sports_bar'],$index);
			$data['sports_slide'] = array_merge($post_active,$pre_active);
		}
		else{
			$data['sports_slide'] = $data['sports_bar'];
		}
		return view('frontend.landing.index',$data);
	}

	/**
	 *  This will load only league part, when time range drop down change. called from ajax
	 *
	 *  @param   Request  $request  request object
	 *  @return  string   load landing page
	 */
	public function postLeagueByTime(Request $request)
	{
		$params			=	str_after($request->url,'sports/');
		if(strlen($params) > 1){
			$param_array	= 	explode('/',$params);
			$sports_slug	=	$param_array[0]	;
		} else {
			$sports_slug	=	config('bet_settings.default_sports_slug','football');
		}

		$country		=	isset($param_array[1]) ? $param_array[1] : null;
		if($country){
			$country	=	Country::select('id')->where('name',$country)->first();
			$country	=	isset($country->id) ? $country->id : null;
		}
		$data			=	$this->getLeagueListData($sports_slug, $country, (int)$request->time_range);
		return view('frontend.landing.league_list',$data); 
	}
}
