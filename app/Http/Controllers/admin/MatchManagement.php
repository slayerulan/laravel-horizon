<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use Session;
use App\Http\Controllers\Controller;
use App\Http\Controllers\admin\AdminBaseController;
use DB;
use App\Sport;
use App\FeedModel\Match;
use App\UsersActivityLog;
use Illuminate\Pagination\Paginator;
use Carbon\Carbon;
/**
 *  This is a command to fetch all bet report management details in admin panel.
 *
 *  @author	Arijit Jana
 */
class MatchManagement extends AdminBaseController
{
    public function index() {
        $this->setLeftSideBarData();
        $sports = Sport::all();
        return view('admin.sports-book.match_management_list',['sports' => $sports, 'parent_menu' => $this->parent_menu, 'sub_menu' =>$this->sub_menu]);
    }

    public function postMatches(request $request) {
    	$sport = $request->sport;
      	$start_date = $request->start_date.' 00:00:00';
      	$end_date = $request->end_date.' 23:59:59';
      	$page = $request->page;
		$offset = ($page-1)*PAGING;
    	$matchesObj = Match::with('sport','league')->whereBetween('start_timestamp', [strtotime($start_date), strtotime($end_date)]);
		if($sport != 'all') {
            $matchesObj->where('sport_id', $sport);
        }
        $total_matches = $matchesObj->count();
        $matchesObj = $matchesObj->orderBy('start_timestamp', 'desc')->offset($offset)->limit(PAGING)->get();
        $data['matches'] = array();
        if (isset($matchesObj)) {
        	foreach ($matchesObj as $matchObj) {
        		$betArray['time'] = date("Y/m/d H:i:s",$matchObj->start_timestamp);
        		$betArray['id'] = $matchObj->id;
        		$betArray['sport'] = $matchObj->sport['name'];
                $betArray['country'] = $matchObj->league->country['name'];
        		$betArray['league'] = $matchObj->league['name'];
        		$betArray['homeTeam'] = $matchObj->home_team['name'];
		      	$betArray['awayTeam'] = $matchObj->away_team['name'];
		        $betArray['score'] = $matchObj->score;
		        $betArray['status'] = $matchObj->status;
		        $data['matches'][] = $betArray;
        	}
        }
        $data['total_page'] = ceil($total_matches/PAGING);
      	$data['active_page'] = $page;

      	return view('admin.sports-book.match_data_list',$data);
    }

    public function postChangeMatchStatus(request $request) {
    	$id = $request->id;
      	$value = $request->value;
    	$update_status = Match::where('id', '=', $id)->update(array('status' => $value));
    	print_r($update_status);
    }
}
