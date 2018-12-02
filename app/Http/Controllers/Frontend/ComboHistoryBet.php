<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\ComboBetslip;
use App\ComboBetslipCombination;
use Carbon\Carbon;


/**
 *  This will contain all functions which are require to load combo bet history for all sports.
 *
 *  @author Atul Verma
 */
class ComboHistoryBet extends FrontendBaseController
{
    /**
	 * this will load combo bet history in page
	 * @return html	 load view page
	 */
    public function getAllBets(){
    	$data = [];
    	$data['sports_bar']		= 	$this->includeSportsBar();
    	$userId = $this->user_id;
    	$comboBetSlipsObj = ComboBetslip::where('user_id', $this->user_id)->with('sport')->orderBy('created_at', 'desc')->paginate(PAGING);
    	$data['betComboSlips'] = array();
    	foreach($comboBetSlipsObj as $betSlip){
    		$betArray = array();
    		$multiple_odds = "";
    		$betArray['sportName'] = $betSlip->sport->slug;
    		$betArray['betPlaceTime'] = $betSlip->created_at;
    		
    		$betArray['prize_amount'] = $betSlip->prize_amount;
    		$betArray['stake_amount'] = $betSlip->stake_amount;
    		$betArray['bet_number'] = $betSlip->bet_number;
    		$betArray['status'] = $betSlip->status;
            $count = 0;
            $multiple_odds = 1;
    		foreach($betSlip->combinations as $combination){
    			$multiple_odds *= (float)$combination->odds_value;
                $count++;
    			
    		}
            $betArray['total_odds'] = $count;
    		$betArray['multiple_odds'] = $multiple_odds;
    		$data['betComboSlips'][] = $betArray; 
    		
    	}
    	$data['paging'] = $comboBetSlipsObj;
        $data['menu'] = "History";
    	return view('frontend.betHistory.bet_history_combo',$data);
    }

    /**
     * Gets the combo bet slips.
     *
     * @param      \Illuminate\Http\Request  $request  The request
     *
     * @return     <view page ajax>                    The combo bet slips.
     */
    public function getComboBetSlips(Request $request){
        $bet_number = $request->bet_number;
        $comboBetObj = ComboBetslip::where("bet_number",$bet_number)->first();
        $betArray = array();
        $betArray['bet_number'] = $bet_number;
        $betArray['combination'] = array();
        foreach($comboBetObj->combinations as $combination){
            $comb = array();
            $comb['id'] = $combination->id;
            $comb['sportName'] = $combination->sport->slug;
            if ($combination->bet_type == 'live') {
                $comb['homeTeam'] = $combination->live_match->home_team;
                $comb['awayTeam'] = $combination->live_match->away_team;
            }
            else{
                $comb['homeTeam'] = $combination->match->home_team->local_name;
                $comb['awayTeam'] = $combination->match->away_team->local_name;
            }
            $comb['market_name'] = $combination->market_name;
            $comb['bet_for'] = $combination->bet_for;
            $comb['extra'] = $combination->extra_value;
            $comb['odds_value'] = $combination->odds_value;
            $comb['status'] = $combination->status;
            $betArray['combination'][] = $comb;
        }
        return view('frontend.betHistory.combo_bets',$betArray);
    }
    /**
     * Gets the combo bet slips for search.
     *
     * @param      \Illuminate\Http\Request  $request  The request
     *
     * @return     <type>                    The combo bet slips.
     */
    public function getComboBetSlipsSearch(Request $request){
        $bet_number = $request->bet_number;
        $comboBetObj = ComboBetslip::where("bet_number",$bet_number)->first();
        $betArray = array();
        $betArray['bet_number'] = $bet_number;
        $betArray['combination'] = array();
        foreach($comboBetObj->combinations as $combination){
            $comb = array();
            $comb['id'] = $combination->id;
            $comb['sportName'] = $combination->sport->slug;
            if ($combination->bet_type == 'live') {
                $comb['homeTeam'] = $combination->live_match->home_team;
                $comb['awayTeam'] = $combination->live_match->away_team;
            }
            else{
                $comb['homeTeam'] = $combination->match->home_team->local_name;
                $comb['awayTeam'] = $combination->match->away_team->local_name;
            }
            $comb['market_name'] = $combination->market_name;
            $comb['bet_for'] = $combination->bet_for;
            $comb['extra'] = $combination->extra_value;
            $comb['odds_value'] = $combination->odds_value;
            $comb['status'] = $combination->status;
            $betArray['combination'][] = $comb;
        }
        return view('frontend.betHistory.combo_bets_search',$betArray);
    }

    /**
     * Gets the bets details.
     *
     * @param      \Illuminate\Http\Request  $request  The request
     *
     * @return     <view page ajax>                    The bets details.
     */
    public function getBetsDetails(Request $request){

        $id = $request->id;

        $betSlipsObj = ComboBetslipCombination::find($id);
        $betDetailArray = array();
        $betDetailArray['sportName'] = $betSlipsObj->sport->name;
        $betDetailArray['bet_type'] = $betSlipsObj->bet_type;
        if ($betSlipsObj->bet_type == 'live') {
            $betDetailArray['country'] = $betSlipsObj->live_match->country;
            $betDetailArray['league'] = $betSlipsObj->live_match->league_name;
            $betDetailArray['homeTeam'] = $betSlipsObj->live_match->home_team;
            $betDetailArray['awayTeam'] = $betSlipsObj->live_match->away_team;
            $betDetailArray['match_date_time'] = $betSlipsObj->live_match->start_timestamp;
            $betDetailArray['score'] = $betSlipsObj->live_match->score;
        }
        else{
            $betDetailArray['country'] = $betSlipsObj->match->league->country->name;
            $betDetailArray['league'] = $betSlipsObj->match->league->name;
            $betDetailArray['homeTeam'] = $betSlipsObj->match->home_team->local_name;
            $betDetailArray['awayTeam'] = $betSlipsObj->match->away_team->local_name;
            $betDetailArray['match_date_time'] = Carbon::createFromTimestamp($betSlipsObj->match->start_timestamp)->toDateTimeString();
            $betDetailArray['score'] = $betSlipsObj->match->score;
        }
        $betDetailArray['market_name'] = $betSlipsObj->market_name;
        $betDetailArray['calculated_odds'] = $betSlipsObj->calculated_odds_value;        
        $betDetailArray['bet_for'] = $betSlipsObj->bet_for;
        $betDetailArray['bet_value'] = $betSlipsObj->odds_value;
        $betDetailArray['result'] = $betSlipsObj->status;
        $data['data'] = $betDetailArray;
        return view('frontend.betHistory.combo_bet_slip_detail',$data);
    }

    /**
     * return data which matches with search value(sport_type, Ref no. and stake )
     *
     * @param      \Illuminate\Http\Request  $request  The request
     *
     * @return     <type>                    ( view page ajax )
     */
    public function searchBetsCombo(Request $request){
        $search = $request->data;
        $userId = $this->user_id;
        if($request->page){
            $page = $request->page;
        }else{
            $page = 1;
        }

        $comboBetSlipsObj = ComboBetslip::where('user_id', $userId)
                             ->with('sport')
                             ->where('bet_number', 'LIKE', '%'.$search.'%')
                             ->orWhere('stake_amount', 'LIKE', '%'.$search.'%')
                             ->orWhereHas('sport', function ($query) use($search) {
                                $query->where('name', 'like','%'.$search.'%');
                            })->orderBy('created_at', 'desc')->get();
                     
        $total_page = ceil(count($comboBetSlipsObj)/PAGING);
        $maxIndex = (int)$page * PAGING;
        $minIndex = ($maxIndex-PAGING);
        $data =[];
        $data['betComboSlips'] = [];
        for($i=$minIndex;$i<$maxIndex;$i++){
            if(isset($comboBetSlipsObj[$i])){
                $betSlip = $comboBetSlipsObj[$i];
                $betArray = array();
                $multiple_odds = "";
                $betArray['sportName'] = $betSlip->sport->slug;
                $betArray['betPlaceTime'] = $betSlip->created_at;
                
                $betArray['prize_amount'] = $betSlip->prize_amount;
                $betArray['stake_amount'] = $betSlip->stake_amount;
                $betArray['bet_number'] = $betSlip->bet_number;
                $betArray['status'] = $betSlip->status;
                $count = 0;
                $multiple_odds = 1;
                foreach($betSlip->combinations as $combination){
                    $multiple_odds *= (float)$combination->odds_value;
                    $count++;
                    
                }
                $betArray['total_odds'] = $count;
                $betArray['multiple_odds'] = $multiple_odds;
                $data['betComboSlips'][] = $betArray;
            }
        }
        $data['total_page'] = $total_page;
        $data['active_page'] = $page;
        return view('frontend.betHistory.search_comboBets',$data); 
    }
}
