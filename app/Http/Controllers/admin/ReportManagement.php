<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use Session;
use App\Http\Controllers\Controller;
use App\Http\Controllers\admin\AdminBaseController;
use DB;
use App\Sport;
use App\Betslip;
use App\ComboBetslip;
use App\ComboBetslipCombination;
use App\User;
use App\UserWallet;
use App\Transaction;
use App\UsersActivityLog;
use Illuminate\Pagination\Paginator;
use Carbon\Carbon;
/**
 *  This is a command to fetch all bet report management details in admin panel.
 *
 *  @author	Sourav Chowdhury
 */
class ReportManagement extends AdminBaseController
{

    /*---------------------------------- Single Bet Start --------------------------------*/
    /**
     * this function is for get single bet report page
     */
    public function getSingleBetReport()
    {
        $this->setLeftSideBarData();
        $sports = Sport::all();
        $user_id = Session::get('user_id');
        $role_id = Session::get('role_id');
        $agents = array();
        $players = '';

        $startDatequery = Betslip::orderBy('created_at', 'asc')->take(1)->first(['created_at']);
        $startDateString = $startDatequery['created_at']->toDateTimeString();
        $startDate = substr($startDateString,0,10);

        $endDatequery = Betslip::orderBy('created_at', 'desc')->take(1)->first(['created_at']);
        $endDateString = $endDatequery['created_at']->toDateTimeString();
        $endDate = substr($endDateString,0,10);

        if($role_id ==1)
        {
            $all_users = User::where('role_id','!=',4)
                          ->whereNull('deleted_at')->get()->toArray();
            array_push($agents,$all_users);

            $players = User::where('role_id',4)
                          ->whereNull('deleted_at')->get();

        }
        else
        {
            $loggedInUser = User::where('id',$user_id)->get()->toArray();
            array_push($agents,$loggedInUser);
            $all_users = User::where('role_id','!=',1)
                        ->where('role_id','!=',4)
                        ->where('agent_id',$user_id)
                        ->whereNull('deleted_at')->get()->toArray();
            if($all_users)
            {
              array_push($agents[0],$all_users[0]);
            }

            $players = User::where('role_id',4)
                          ->where('agent_id',$user_id)
                          ->whereNull('deleted_at')->get();
        }
        return view('admin.report.single.single_bet_report',['sports' => $sports, 'start_date' => $startDate, 'end_date' => $endDate, 'agents' => $agents, 'players' => $players, 'parent_menu' => $this->parent_menu, 'sub_menu' =>$this->sub_menu]);
    }
    /**
     * this function is for show single bet report on page
     */
    public function postSingleBetReport(request $request)
    {
      $user_id = Session::get('user_id');
      $role_id = Session::get('role_id');

      $playerArray = array();

      $data = [];
      $sport = $request->sport;
      $start_date = $request->start_date;
      $end_date = $request->end_date;
      $status = $request->status;
      $agent = $request->agent;
      $player = $request->player;
      $bet_type = $request->bet_type;
      //p($bet_type);

      if($request->page)
      {
          $page = $request->page;
      }
      else
      {
          $page = 1;
      }

        $betSlipsObj = Betslip::with('sport','user','match','match.home_team','match.away_team')
                     ->whereBetween(DB::raw('DATE(created_at)'), [$start_date, $end_date]);
                     if($sport != 'all')
                     {
                        $betSlipsObj->where('sport_id', $sport);
                     }
                     if($status != 'all')
                     {
                        $betSlipsObj->where('status', $status);
                     }
                     if(isset($player))
                     {
                        $betSlipsObj->where('user_id', $player);
                     }
                     if(isset($bet_type))
                     {
                        $betSlipsObj->where('bet_type', $bet_type);
                     }
                     if(isset($agent))
                     {
                        $allPlayers = DB::select( DB::raw("SELECT id FROM users WHERE role_id=4 AND deleted_at is null AND agent_id IN (SELECT DISTINCT id from users WHERE agent_id=$agent) OR agent_id=$agent AND role_id =4 AND deleted_at is null") );
                        if($allPlayers)
                        {
                          foreach($allPlayers as $pdata)
                          {
                             array_push($playerArray,$pdata->id);
                          }
                        }
                        $betSlipsObj->whereIn('user_id', $playerArray);
                     }
                     else
                     {
                        if($role_id != 1)
                        {
                            $allPlayers = DB::select( DB::raw("SELECT id FROM users WHERE role_id=4 AND deleted_at is null AND agent_id IN (SELECT DISTINCT id from users WHERE agent_id=$user_id) OR agent_id=$user_id AND role_id =4 AND deleted_at is null") );

                            if($allPlayers)
                            {
                              foreach($allPlayers as $pdata)
                              {
                                 array_push($playerArray,$pdata->id);
                              }
                            }
                            $betSlipsObj->whereIn('user_id', $playerArray);
                        }
                     }
      $betSlipsObj = $betSlipsObj->orderBy('created_at', 'desc')->get();

      $total_page = ceil(count($betSlipsObj)/PAGING);
      $maxIndex = (int)$page * PAGING;
      $minIndex = ($maxIndex-PAGING);

      $data['betslips'] = array();

          for($i=$minIndex;$i<$maxIndex;$i++)
          {
                if(isset($betSlipsObj[$i]))
                {
                    $betSlip = $betSlipsObj[$i];
                    $betArray = array();

                    $betArray['agent'] = '';
                    if (!empty($betSlip->user->agent_id)) {
                      $agentData = User::where('id', $betSlip->user->agent_id)->first();
                      $betArray['agent'] = $agentData->username;
                    }
                    $playerName = User::where('id', $betSlip->user->id)->first()->username;
                    $sportName = Sport::where('id', $betSlip->sport_id)->first()->name;

                        $betArray['id'] = $betSlip->id;
                        $betArray['player'] = $playerName;

                        if(($bet_type == 'pre') && ($betSlip->bet_type == 'pre'))
                        {
                            $betArray['homeTeam'] = $betSlip->match->home_team['name'];
                            $betArray['awayTeam'] = $betSlip->match->away_team['name'];
                            $betArray['score'] = $betSlip->match->score;
                        }
                        else if(($bet_type == 'live') && ($betSlip->bet_type == 'live'))
                        {
                            $betArray['homeTeam'] = $betSlip->live_match->home_team;
                            $betArray['awayTeam'] = $betSlip->live_match->away_team;
                            $betArray['score'] = $betSlip->live_match->score;
                        }

                        $betArray['stakeAmount'] = $betSlip->stake_amount;
                        $betArray['marketName'] = $betSlip->market_name;
                        $betArray['refNo'] = $betSlip->bet_number;
                        $betArray['sport'] = $sportName;
                        $betArray['betFor'] = $betSlip->bet_for;
                        $betArray['extraValue'] = $betSlip->extra_value;
                        $betArray['oddsValue'] = $betSlip->odds_value;
                        $betArray['prizeAmount'] = $betSlip->prize_amount;
                        $betArray['betPlaceTime'] = $betSlip->created_at;
                        $betArray['status'] = $betSlip->status;


                    $data['betslips'][] = $betArray;
                }

            }

      $data['total_page'] = $total_page;
      $data['active_page'] = $page;
      return view('admin.report.single.single_bet_report_data',$data);
    }

    /**
     * this function is for delete single bet report
     */
    public function postSingleBetReportDelete(request $request)
    {
        $loggedInUser = Session::get('user_id');
        $betSlip = Betslip::where('id',$request->id)->first();
        $betNumber = $betSlip->bet_number;
        $userId = $betSlip->user_id;
        $stakeAmount = $betSlip->stake_amount;

        Betslip::where('id', $request->id)->update(['status' => 'cancel']);

        $userWallet = UserWallet::where('user_id', $userId)->first();
        $userWalletId = $userWallet->id;
        $newWalletAmount = $userWallet->amount+$stakeAmount;

        UserWallet::where('user_id', $userId)->update(['amount' => $newWalletAmount]);

        $userActivityLog = new UsersActivityLog;
        $userActivityLog->user_id = $loggedInUser;
        $userActivityLog->event = 'Single Bet Report Deleted From Admin';
        $userActivityLog->save();

        $transaction = new Transaction;
        $transaction->user_wallet_id = $userWalletId;
        $transaction->type = 'credit';
        $transaction->title = "Single Bet Refund [".$betNumber."]";
        $transaction->amount = $stakeAmount;
        $transaction->save();

        echo 'true';
    }

    /*---------------------------------- Single Bet End --------------------------------*/

    /*---------------------------------- Combo Bet Start --------------------------------*/
    /**
     * this function is for get combo bet report page
     */
    public function getComboBetReport()
    {
        $this->setLeftSideBarData();
        $sports = Sport::all();
        $user_id = Session::get('user_id');
        $role_id = Session::get('role_id');
        $agents = array();
        $players = '';

        $startDatequery = ComboBetslip::orderBy('created_at', 'asc')->take(1)->first(['created_at']);
        if (!empty($startDatequery)) {
          $startDateString = $startDatequery['created_at']->toDateTimeString();
          $startDate = substr($startDateString,0,10);
          $endDatequery = ComboBetslip::orderBy('created_at', 'desc')->take(1)->first(['created_at']);
          $endDateString = $endDatequery['created_at']->toDateTimeString();
          $endDate = substr($endDateString,0,10);
        }
        else{
          $startDate = date('Y-m-d');
          $endDate = date('Y-m-d');
        }

        if($role_id ==1)
        {
            $all_users = User::where('role_id','!=',4)
                          ->whereNull('deleted_at')->get()->toArray();
            array_push($agents,$all_users);

            $players = User::where('role_id',4)
                          ->whereNull('deleted_at')->get();

        }
        else
        {
            $loggedInUser = User::where('id',$user_id)->get()->toArray();
            array_push($agents,$loggedInUser);
            $all_users = User::where('role_id','!=',1)
                        ->where('role_id','!=',4)
                        ->where('agent_id',$user_id)
                        ->whereNull('deleted_at')->get()->toArray();

            if($all_users)
            {
              array_push($agents[0],$all_users[0]);
            }

            $players = User::where('role_id',4)
                          ->where('agent_id',$user_id)
                          ->whereNull('deleted_at')->get();

        }
        return view('admin.report.combo.combo_bet_report',['sports' => $sports, 'start_date' => $startDate, 'end_date' => $endDate, 'agents' => $agents, 'players' => $players, 'parent_menu' => $this->parent_menu, 'sub_menu' =>$this->sub_menu]);
    }

    /**
     * this function is for show combo bet report on page
     */
    public function postComboBetReport(request $request)
    {
        $user_id = Session::get('user_id');
        $role_id = Session::get('role_id');

        $playerArray = array();

        $data = [];
        $sport = $request->sport;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $status = $request->status;
        $agent = $request->agent;
        $player = $request->player;

        if($request->page)
        {
            $page = $request->page;
        }
        else
        {
            $page = 1;
        }

        $comboBetSlipsObj = ComboBetslip::with('sport','user')
                   ->whereBetween(DB::raw('DATE(created_at)'), [$start_date, $end_date]);
                   if($sport != 'all')
                   {
                      $comboBetSlipsObj->where('sport_id', $sport);
                   }
                   if($status != 'all')
                   {
                      $comboBetSlipsObj->where('status', $status);
                   }
                   if(isset($player))
                   {
                      $comboBetSlipsObj->where('user_id', $player);
                   }
                   if(isset($agent))
                   {
                        $allPlayers = DB::select( DB::raw("SELECT id FROM users WHERE role_id=4 AND deleted_at is null AND agent_id IN (SELECT DISTINCT id from users WHERE agent_id=$agent) OR agent_id=$agent AND role_id =4 AND deleted_at is null") );
                        if($allPlayers)
                        {
                          foreach($allPlayers as $pdata)
                          {
                             array_push($playerArray,$pdata->id);
                          }
                        }

                        $comboBetSlipsObj->whereIn('user_id', $playerArray);
                   }
                   else
                   {
                        if($role_id != 1)
                        {
                            $allPlayers = DB::select( DB::raw("SELECT id FROM users WHERE role_id=4 AND deleted_at is null AND agent_id IN (SELECT DISTINCT id from users WHERE agent_id=$user_id) OR agent_id=$user_id AND role_id =4 AND deleted_at is null") );

                            if($allPlayers)
                            {
                              foreach($allPlayers as $pdata)
                              {
                                 array_push($playerArray,$pdata->id);
                              }
                            }
                            $comboBetSlipsObj->whereIn('user_id', $playerArray);
                        }
                  }

          $comboBetSlipsObj = $comboBetSlipsObj->orderBy('created_at', 'desc')->get();
          $data['betComboSlips'] = array();
          //echo(count($comboBetSlipsObj));
          $total_page = ceil(count($comboBetSlipsObj)/PAGING);
          $maxIndex = (int)$page * PAGING;
          $minIndex = ($maxIndex-PAGING);

          for($i=$minIndex;$i<$maxIndex;$i++)
          {
            if(isset($comboBetSlipsObj[$i]))
            {
                $betSlip = $comboBetSlipsObj[$i];
                $betArray = array();
                $multiple_odds = "";

                $sportName = Sport::where('id', $betSlip->sport_id)->first()->name;
                $agentData = User::where('id', $betSlip->user->agent_id)->first();

                  $betArray['id'] = $betSlip->id;
                  $betArray['sportName'] = $betSlip->sport->slug;
                  $betArray['betPlaceTime'] = $betSlip->created_at;
                  $betArray['prize_amount'] = $betSlip->prize_amount;
                  $betArray['stake_amount'] = $betSlip->stake_amount;
                  $betArray['bet_number'] = $betSlip->bet_number;
                  $betArray['status'] = $betSlip->status;
                  $count = 0;
                  $multiple_odds = 1;
                  foreach($betSlip->combinations as $combination)
                  {
                      $multiple_odds *= (float)$combination->odds_value;
                      $count++;
                  }
                  $betArray['total_odds'] = $count;
                  $betArray['multiple_odds'] = number_format((float)$multiple_odds, 2, '.', '');

                  $data['betComboSlips'][] = $betArray;
            }
          }

          $data['total_page'] = $total_page;
          $data['active_page'] = $page;

        return view('admin.report.combo.combo_bet_report_data',$data);
    }

    /**
     * this function is for show combo bet slips on page
     */
    public function postComboBetSlip(request $request)
    {

        $bet_number = $request->bet_number;
        $bet_type = $request->bet_type;

        $comboBetObj = ComboBetslip::with('sport','user')->where("bet_number",$bet_number)->first();
        $betArray = array();
        $betArray['bet_number'] = $bet_number;
        $betArray['combination'] = array();

            foreach($comboBetObj->combinations as $combination)
            {
                $agentData = User::where('id', $comboBetObj->user->agent_id)->first();
                $playerName = User::where('id', $comboBetObj->user->id)->first()->username;

                $comb = array();
                if($bet_type == 'pre')
                {
                      if($combination['bet_type'] == 'pre')
                      {
                        $comb['id'] = $combination->id;
                        $comb['agent'] = $agentData->username;
                        $comb['player'] = $playerName;
                        $comb['sportName'] = $combination->sport->name;
                        $comb['homeTeam'] = $combination->match->home_team->local_name;
                        $comb['awayTeam'] = $combination->match->away_team->local_name;
                        $comb['market_name'] = $combination->market_name;
                        $comb['bet_for'] = $combination->bet_for;
                        $comb['extra'] = $combination->extra_value;
                        $comb['odds_value'] = number_format((float)$combination->odds_value, 2, '.', '');
                        $comb['status'] = $combination->status;
                      }
                  }
                  else
                  {
                      if($combination['bet_type'] == 'live')
                      {
                        $comb['id'] = $combination->id;
                        $comb['agent'] = $agentData->username;
                        $comb['player'] = $playerName;
                        $comb['sportName'] = $combination->sport->name;
                        $comb['homeTeam'] = $combination->live_match->home_team;
                        $comb['awayTeam'] = $combination->live_match->away_team;
                        $comb['market_name'] = $combination->market_name;
                        $comb['bet_for'] = $combination->bet_for;
                        $comb['extra'] = $combination->extra_value;
                        $comb['odds_value'] = number_format((float)$combination->odds_value, 2, '.', '');
                        $comb['status'] = $combination->status;
                      }
                  }
                $betArray['combination'][] = $comb;
            }
        return view('admin.report.combo.combo_bet_slip_data',$betArray);
    }

  /**
   * this function is for show combo bet slip details on modal
   */
    public function postComboBetSlipDetails(request $request)
    {
        $combo_betslip_id = $request->comboBetslipId;
        $betSlipsObj = ComboBetslipCombination::find($combo_betslip_id);


            $betSlips = ComboBetslip::with('sport','user')->where('id',$betSlipsObj->combo_betslip_id)->first();
            $agentData = User::where('id', $betSlips->user->agent_id)->first();
            $playerName = User::where('id', $betSlips->user->id)->first()->username;

            $betDetailArray = array();
            $betDetailArray['combo_betslip_id'] = $combo_betslip_id;
            $betDetailArray['agent'] = $agentData->username;
            $betDetailArray['player'] = $playerName;
            $betDetailArray['sportName'] = $betSlipsObj->sport->name;

            if($betSlipsObj->bet_type == 'pre')
            {
                $betDetailArray['country'] = $betSlipsObj->match->league->country->name;
                $betDetailArray['league'] = $betSlipsObj->match->league->name;
                $betDetailArray['homeTeam'] = $betSlipsObj->match->home_team->local_name;
                $betDetailArray['awayTeam'] = $betSlipsObj->match->away_team->local_name;
                $betDetailArray['score'] = $betSlipsObj->match->score;
                $betDetailArray['match_date_time'] = Carbon::createFromTimestamp($betSlipsObj->match->start_timestamp)->toDateTimeString();
            }
            else
            {
                $betDetailArray['country'] = $betSlipsObj->live_match->country;
                $betDetailArray['league'] = $betSlipsObj->live_match->league_name;
                $betDetailArray['homeTeam'] = $betSlipsObj->live_match->home_team;
                $betDetailArray['awayTeam'] = $betSlipsObj->live_match->away_team;
                $betDetailArray['score'] = $betSlipsObj->live_match->score;
                $betDetailArray['match_date_time'] = $betSlipsObj->live_match->start_timestamp;
            }
            $betDetailArray['market_name'] = $betSlipsObj->market_name;
            $betDetailArray['calculated_odds'] = $betSlipsObj->calculated_odds_value;

            $betDetailArray['bet_for'] = $betSlipsObj->bet_for;
            $betDetailArray['bet_value'] = number_format((float)$betSlipsObj->odds_value, 2, '.', '');


            $betDetailArray['result'] = $betSlipsObj->status;
        $data['data'] = $betDetailArray;

        return view('admin.report.combo.combo_bet_slip_details',$data);
    }

    /**
     * this function is for delete combo bet report
     */
    public function postComboBetReportDelete(request $request)
    {
        $loggedInUser = Session::get('user_id');
        $comboBetSlip = ComboBetslip::where('id',$request->id)->first();
        $betNumber = $comboBetSlip->bet_number;
        $userId = $comboBetSlip->user_id;
        $stakeAmount = $comboBetSlip->stake_amount;

        ComboBetslip::where('id', $request->id)->update(['status' => 'cancel']);

        $comboBetslipCombinations = ComboBetslipCombination::where('combo_betslip_id',$request->id)->get();
        foreach($comboBetslipCombinations as $comboBetslipCombinationsData)
        {
           ComboBetslipCombination::where('id', $comboBetslipCombinationsData->id)->update(['status' => 'cancel']);
        }

        $userWallet = UserWallet::where('user_id', $userId)->first();
        $userWalletId = $userWallet->id;
        $newWalletAmount = $userWallet->amount+$stakeAmount;

        UserWallet::where('user_id', $userId)->update(['amount' => $newWalletAmount]);

        $userActivityLog = new UsersActivityLog;
        $userActivityLog->user_id = $loggedInUser;
        $userActivityLog->event = 'Combo Bet Report Deleted From Admin';
        $userActivityLog->save();

        $transaction = new Transaction;
        $transaction->user_wallet_id = $userWalletId;
        $transaction->type = 'credit';
        $transaction->title = "Combo Bet Refund [".$betNumber."]";
        $transaction->amount = $stakeAmount;
        $transaction->save();

        echo 'true';
    }
    /*---------------------------------- Combo Bet End --------------------------------*/
}
