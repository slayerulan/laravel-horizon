<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use App\Http\Traits\Authentication;
use App\User;
use App\FeedModel\Sport;
use App\Http\Traits\Listing\Match;
use App\Http\Traits\Listing\League;
use App\OddsEditing;
use App\BetRule;
use App\ConfluxAgent;


/**
 *  this is base controller for front end. Evry controller should inherit this.
 *
 *  @author Anirban Saha
 */
class FrontendBaseController extends Controller
{
	use Authentication, League,  Match;
	/**
	 * current logged in user's details
	 *
	 * @var object users table object
	 */
	public $user;

	/**
	 * Details of the agent of the logged user.
	 */
	public $agent;

	/**
	 * authentication token to comunicate with conflux site
	 */
	public $sessionToken;

	/**
	 *  this will assign user role and user in class property
	 *
	 */
	public function __construct(Request $request)
	{
		ini_set('max_execution_time', 0);
		ini_set('memory_limit','1000M');
		$this->setOddsValueType();
		// a(Session::get('user_details'));
	 	$check = $this->isLoggedIn($request);
		if ($check == true) {
	 		$this->user = User::find($this->user_id);
			// if (isset(Session::get('conf_user_details')->agent->agentId) && !empty(Session::get('conf_user_details')->agent->agentId)) {
				// $agent_id = Session::get('conf_user_details')->agent->agentId;
				// $user_id = ConfluxAgent::where('conflux_agent_id', $agent_id)->first()->user_id;
				// $agent_details = User::find($user_id);
				// if (!empty($agent_details->toArray())) {
				// 	$this->agent = $agent_details;
				// }
			// }
			$this->setBetRule();
			$this->sessionToken = Session::get('conf_user_details')->sessionToken;
	 	}
	}
	/**
	 * Saves the default selected odds value display type in session if already not set.
	 */
	public function setOddsValueType() {
		if (Session::get('odds_value_type') === null) {
			Session::put('odds_value_type', 'Decimal');
		}
	}

	/**
	 * Sets the bet rule for a user based on his agent.
	 */
	public function setBetRule() {
		if (empty(Session::get('user_details')['bet_rule'])) {
			Session::push('user_details.bet_rule',$this->user->getBetRule());
		}
	}

	/**
	 *  this will return left bar data, it will cache data for 60 minute
	 *
	 *  @return  array  sport, country data with count
	 */
	public function includeSportsBar()
	{
		$sports_list	= Cache::remember('left_sports_bar',60, function () {
			$sports	= Cache::remember('active_sports', 60*24, function () {
				return Sport::where('status','active')->where('fetch_feed','yes')->get();
			});
			$sports_list	=	[];
			if($sports->isNotEmpty()){
				foreach ($sports as $key => $value) {
					$data  				=  new \stdClass();
					$data->sport_name	=	$value->name;
					$data->sport_slug	=	$value->slug;
					$data->countries	=	$value->getCountryWithLeagueCount();
					$sports_list[]		=	$data;
				}
			}
			return $sports_list;
		});
		return $sports_list;
	}

	/**
	 *  this will return left bar data as well as league list data
	 *
	 *  @param   string  $sport_slug  sport name
	 *  @param   null|int  $country_id  null will return all country data, id will filter
	 *  @param   null|int  $time_range  null will return default maximum range, int will filter by time
	 *
	 *  @return  array  data for left bar and league list
	 */
	public function getLeagueListData($sport_slug, $country_id = null, $time_range = null)
	{
		$data					=	[];
		$data['sports_bar']		= 	$this->includeSportsBar();
		$data['sport_details']	=	$this->getLeagues($sport_slug,$country_id,$time_range);
		return $data;
	}

	/**
	*  This will return left bar data as well as match list data
	 *
	 *  @param  string  $sport_slug  sport slug
	 *  @param  array  $league_ids  league ids
	 *  @param  int  $time_range  time range
	 *
	 *  @return  array  data for left bar and match list
	 */
	public function getMatchesListData($sport_slug, $league_ids, $time_range)
	{
		$data					=	[];
		$data					=	$this->getMatches($sport_slug, $league_ids, $time_range);
		$data['sports_bar']		= 	$this->includeSportsBar();
		$this->getUsersEditedOddValue($sport_slug);
		return $data;
	}

	/**
	 *  this will return bookmaker of current user
	 *
	 *  @return  int  bookmaker id
	 */
	public function getBookmaker()
	{
		return getBetRule()->bookmaker;
	}

	/**
	 * sets the odds editing value for a user if exist, else sets the default odds editing value in the session.
	 *
	 * @param      string    $sport_slug    slug of the selected sport
	 */
	public function getUsersEditedOddValue($sport_slug) {
		$sport_id = Sport::where('slug',$sport_slug)->first()->id;
		if (isset($this->user) && !empty($this->user->agent_id)) {
			$user = $this->user;
			Session::put('users_odd', '');
			if (!empty($user->getOddsEditing('Sport', $sport_id)->toArray())) {
				$users_odd = $user->getOddsEditing('Sport', $sport_id);
				Session::put('users_odd',$users_odd);
			}
			elseif (!empty($user->getOddsEditing('Bookmaker')->toArray())) {
				$users_odd = $user->getOddsEditing('Bookmaker');
				Session::put('users_odd',$users_odd);
			}
			if (Session::get('users_odd') == '') {
				$this->setDefaultOddsEditing($sport_id);
			}
		}
		else{
			$this->setDefaultOddsEditing($sport_id);
		}
	}

	/**
	 * Sets the default odds editing value in the session.
	 *
	 * @param      integer    $sport_id    The sport identifier of the selected sport.
	 */
	public function setDefaultOddsEditing($sport_id=null) {
		Session::put('users_odd', '');
		if (!empty($this->getDefaultOddsEditing('Sport', $sport_id)->toArray())) {
			$users_odd = $this->getDefaultOddsEditing('Sport', $sport_id);
			Session::put('users_odd',$users_odd);
		}
		elseif (!empty($this->getDefaultOddsEditing('Bookmaker')->toArray())) {
			$users_odd = $this->getDefaultOddsEditing('Bookmaker');
			Session::put('users_odd',$users_odd);
		}
		// else{
		// 	Session::put('users_odd', '');
		// }
	}

	/**
	 * fetches the default odds editing values from the database.
	 *
	 * @param      string    $type        Sport / Bookmaker.
	 * @param      integer   $sport_id    The sport identifier of the selected sport.
	 *
	 * @return     array    The default odds editing value.
	 */
	public function getDefaultOddsEditing($type, $sport_id=null) {
		if ($type == "Sport") {
			return OddsEditing::where('is_default', 'yes')->where('type', $type)->where('sport_id', $sport_id)->where('status', 'active')->get();
		}
		else{
			return OddsEditing::where('is_default', 'yes')->where('type', $type)->where('status', 'active')->get();
		}
	}
}
