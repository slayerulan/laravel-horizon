<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use App\LtmTranslation;

/**
 *  this controller will do several function
 *
 *  @author Anirban Saha
 */
class Tools extends Controller
{
	public function __construct()
	{
		ini_set('max_execution_time', 0);
		ini_set('memory_limit','1000M');
	}
	/**
	 * this will check an unique field
	 *
	 * @param  Request $request request object
	 * @return bool           1 for not exist, 0 for exist
	 */
    public function postCheckUnique(Request $request)
    {
    	$field = $request->field;
    	$value = $request->value;
    	$id = $request->id;
    	$table = $request->table ? $request->table : 'users';
		$checking = DB::table($table)->where($field,$value)->get();
		if(isset($checking[0]) && $checking[0]->id != $id){
			echo 0;
		}else{
			echo 1;
		}
    }
	/**
	 * this will change site language and set into Session
	 *
	 * @param  Request $request request object
	 * @return void
	 */
	public function postChangeLanguage(Request $request)
	{
		$lang = $request->lang;
		if(array_key_exists($lang,config('app.lang_name'))){
			Session::put('selected_lang',$lang);
		}
	}
	/**
	 * Saves the selected odds value display type in session.
	 *
	 * @param      \Illuminate\Http\Request  	$request  	The requested odds value display type
	 */
	public function postChangeOddsType(Request $request) {
		$odds_type = $request->odds_type;
		if ($odds_type == 'Decimal' || $odds_type == 'American') {
			Session::put('odds_value_type', $odds_type);
		}
	}

	// public function showSession(Request $request)
	// {
	// 	a($request->session()->all());
	// }
	public function showSession(Request $reques )
	{
		$team_names = DB::select('SELECT teams.name FROM teams LEFT OUTER JOIN ltm_translations ON (teams.name=ltm_translations.key) WHERE ltm_translations.key IS NULL ');
		$country_names = DB::select('SELECT countries.name FROM countries LEFT OUTER JOIN ltm_translations ON (countries.name=ltm_translations.key) WHERE ltm_translations.key IS NULL ');
		$league_names = DB::select('SELECT leagues.name FROM leagues LEFT OUTER JOIN ltm_translations ON (leagues.name=ltm_translations.key) WHERE ltm_translations.key IS NULL ');
		$insert_array	=	[];
		if($team_names) {
			foreach ($team_names as  $value) {
				$each_translation 	=	[];
				$each_translation['locale']		=	'en';
				$each_translation['group']		=	'team';
				$each_translation['key']		=	$value->name;
				$each_translation['value']		=	$value->name;
				// $each_translation['created_at']	=	now();
				$insert_array[]					=	$each_translation;
			}
		}
		if($country_names) {
			foreach ($country_names as  $value) {
				$each_translation 	=	[];
				$each_translation['locale']		=	'en';
				$each_translation['group']		=	'country';
				$each_translation['key']		=	$value->name;
				$each_translation['value']		=	$value->name;
				// $each_translation['created_at']	=	now();
				$insert_array[]					=	$each_translation;
			}
		}
		if($league_names) {
			foreach ($league_names as  $value) {
				$each_translation 	=	[];
				$each_translation['locale']		=	'en';
				$each_translation['group']		=	'league';
				$each_translation['key']		=	$value->name;
				$each_translation['value']		=	$value->name;
				// $each_translation['created_at']	=	now();
				$insert_array[]					=	$each_translation;
			}
		}
		foreach (array_chunk($insert_array,1000) as $rows) {
			//saved to database
			LtmTranslation::insert($rows);
		}
		a($insert_array);
	}
}
