<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Betslip;
use Carbon\Carbon;


/**
 *  This will contain all functions which are require to load single bet history for all sports.
 *
 *  @author Atul Verma
 */
class SingleHistoryBet extends FrontendBaseController
{
	/**
	 * this will load single bet history in page
	 * @return html	 load view page
	 */
    public function getAllBets(){
    	$data = [];
    	$data['sports_bar']		= 	$this->includeSportsBar();
    	$userId = $this->user_id;
    	$betSlipsObj = Betslip::where('user_id', $this->user_id)->with('sport','match.home_team','match.away_team','live_match')->orderBy('id', 'desc')->paginate(PAGING);
    	$count = 0;
        $data['betslips'] = array();
    	foreach($betSlipsObj as $betSlip){
    		$betArray = array();
    		$betArray['sportName'] = $betSlip->sport->slug;
    		$betArray['betPlaceTime'] = $betSlip->created_at;
            if ($betSlip->bet_type == 'live') {
                $betArray['homeTeam'] = $betSlip->live_match->home_team;
                $betArray['ayawTeam'] = $betSlip->live_match->away_team;
            }
            else{
                $betArray['homeTeam'] = $betSlip->match->home_team->local_name;
                $betArray['ayawTeam'] = $betSlip->match->away_team->local_name;
            }
    		$betArray['market_name'] = $betSlip->market_name;
    		$betArray['bet_for'] = $betSlip->bet_for;
    		$betArray['extra'] = $betSlip->extra_value;
    		$betArray['stake_amount'] = $betSlip->stake_amount;
    		$betArray['prize_amount'] = $betSlip->prize_amount;
    		$betArray['bet_number'] = $betSlip->bet_number;
    		$betArray['status'] = $betSlip->status;
    		$data['betslips'][] = $betArray;
    		$count++;
    	}
    	$data['paging'] = $betSlipsObj;
        $data['menu'] = "History";
    	return view('frontend.betHistory.bet_history_single',$data);
    }

    /**
     * Gets the bets details.
     *
     * @param      \Illuminate\Http\Request  $request  The request
     *
     * @return     <view page ajax>                    The bets details.
     */
    public function getBetsDetails(Request $request){
    	$bet_number = $request->bet_number;
    	$betSlipsObj = Betslip::where('bet_number',$bet_number)->first();
    	$betDetailArray = array();
    	$betDetailArray['sportName'] = $betSlipsObj->sport->name;
    	$betDetailArray['ref_nos'] = $bet_number;
        $betDetailArray['bet_type'] = $betSlipsObj->bet_type;
        if ($betSlipsObj->bet_type == 'live') {
            $betDetailArray['country'] = $betSlipsObj->live_match->country;
            $betDetailArray['league'] = $betSlipsObj->live_match->league_name;
            $betDetailArray['homeTeam'] = $betSlipsObj->live_match->home_team;
            $betDetailArray['ayawTeam'] = $betSlipsObj->live_match->away_team;
            $betDetailArray['match_date_time'] = $betSlipsObj->live_match->start_timestamp;
            $betDetailArray['score'] = $betSlipsObj->live_match->score;
        }
        else{
            $betDetailArray['country'] = $betSlipsObj->match->league->country->name;
            $betDetailArray['league'] = $betSlipsObj->match->league->name;
            $betDetailArray['homeTeam'] = $betSlipsObj->match->home_team->local_name;
            $betDetailArray['ayawTeam'] = $betSlipsObj->match->away_team->local_name;
            $betDetailArray['match_date_time'] = Carbon::createFromTimestamp($betSlipsObj->match->start_timestamp)->toDateTimeString();
            $betDetailArray['score'] = $betSlipsObj->match->score;
        }
        $betDetailArray['market_name'] = $betSlipsObj->market_name;
        $betDetailArray['stake_amount'] = $betSlipsObj->stake_amount;
    	$betDetailArray['bet_for'] = $betSlipsObj->bet_for;
    	$betDetailArray['bet_value'] = $betSlipsObj->odds_value;
    	$betDetailArray['result'] = $betSlipsObj->status;
        $data['data'] = $betDetailArray;
    	return view('frontend.betHistory.bet_detail_single',$data);
    }

    /**
     * return data which matches with search value(sport_type, Ref no. and stake )
     *
     * @param      \Illuminate\Http\Request  $request  The request
     *
     * @return     <type>                    ( view page )
     */
    public function searchBetsSingle(Request $request){
        $search = $request->data;
        $userId = $this->user_id;
        if($request->page){
            $page = $request->page;
        }else{
            $page = 1;
        }

        $betSlipsObj = Betslip::where('user_id', $userId)
                     ->with('sport','match.home_team','match.away_team','live_match')
                     ->where('bet_number', 'LIKE', '%'.$search.'%')
                     ->orWhere('stake_amount', 'LIKE', '%'.$search.'%')
                     ->orWhereHas('sport', function ($query) use($search) {
                        $query->where('name', 'like','%'.$search.'%');
                     })->orderBy('id', 'desc')->get();
        $total_page = ceil(count($betSlipsObj)/PAGING);
        $maxIndex = (int)$page * PAGING;
        $minIndex = ($maxIndex-PAGING);
        $data =[];
        $data['betslips'] = [];
        $count = 0;
        for($i=$minIndex;$i<$maxIndex;$i++){
            if(isset($betSlipsObj[$i])){
                $betSlip = $betSlipsObj[$i];
                $betArray = array();
                $betArray['sportName'] = $betSlip->sport->slug;
                $betArray['betPlaceTime'] = $betSlip->created_at;
                if ($betSlip->bet_type == 'live') {
                    $betArray['homeTeam'] = $betSlip->live_match->home_team;
                    $betArray['ayawTeam'] = $betSlip->live_match->away_team;
                }
                else{
                    $betArray['homeTeam'] = $betSlip->match->home_team->local_name;
                    $betArray['ayawTeam'] = $betSlip->match->away_team->local_name;
                }
                $betArray['market_name'] = $betSlip->market_name;
                $betArray['bet_for'] = $betSlip->bet_for;
                $betArray['extra'] = $betSlip->extra_value;
                $betArray['stake_amount'] = $betSlip->stake_amount;
                $betArray['prize_amount'] = $betSlip->prize_amount;
                $betArray['bet_number'] = $betSlip->bet_number;
                $betArray['status'] = $betSlip->status;
                $data['betslips'][] = $betArray;
            }
        }
        $data['total_page'] = $total_page;
        $data['active_page'] = $page;
        return view('frontend.betHistory.search_singlebets',$data);
    }
}
