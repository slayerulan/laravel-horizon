<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\CmsPage;

/**
 *  this is Landing controller will manage some default action of site
 *
 *  @author Anirban Saha
 */
class Landing extends FrontendBaseController
{
    /**
     *  this will load home page of site
     *
     *  @return  string  load html page
     */
    public function index() {
        $all_league   =   $this->getLeagueListData(config('bet_settings.default_sports_slug','football'))['sport_details'];
        $league_data = $this->getLeagueListData(config('bet_settings.default_sports_slug','football'), null, 3);
        // $sport_details = $league_data['sport_details'];
        $league_ids = [];
        foreach ($league_data['sport_details']->current_leagues['league_details'] as $id => $league) {
            $league_ids[] = $id;
        }
        $sport = config('bet_settings.default_sports_slug','football');
        if (!empty($league_ids)) {
            $home_data = $this->getMatchesListData($sport, $league_ids, 3);
        }
        else{
            $home_data = $league_data;
        }
        $home_data['sport_details'] = $all_league;
        $home_data['sport_active'] = $sport;
        $home_data['menu'] = "Home";
        // dd($home_data);
        return view('frontend.landing.home',$home_data);
    }
    /**
     * this will load the recent matches in home page with ajax call
     * @return html             list of matches with odds
     */
    public function postHomeMatch(Request $request) {
        $sport = $request->sport;
        $all_league = $this->getLeagueListData($sport)['sport_details'];
        $league_data = $this->getLeagueListData($sport, null, 3);
        // $sport_details = $league_data['sport_details'];
        $league_ids = [];
        foreach ($league_data['sport_details']->current_leagues['league_details'] as $id => $league) {
            $league_ids[] = $id;
        }
        if (!empty($league_ids)) {
            $home_data = $this->getMatchesListData($sport, $league_ids, 3);
        }
        else{
            $home_data = $league_data;
        }
        $home_data['sport_details'] = $all_league;
        $home_data['active_sport'] = $sport;
        return view('frontend.matches.home_page_listing',$home_data);
    }
    /**
     *  this will load cms page
     *
     *  @param   string  $slug  slug of cms page
     *  @return  string  load html page
     */
    public function getView($slug)
    {
        $details = CmsPage::where('slug_name',$slug)->firstOrFail();
        $data['content'] = $details->content;
        return view('frontend.landing.cms',$data);
    }

    public function getNoDataFound()
    {
        abort(404);
    }
}
