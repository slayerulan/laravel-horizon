<?php

namespace App\Jobs;

use DB;
use Artisan;
use App\LtmTranslation;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 *  this will update new translation keys and export them.
 *
 *  @author Anirban Saha
 */
class UpdateTranslationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
		$team_names = DB::select('SELECT teams.name FROM teams LEFT OUTER JOIN ltm_translations ON (teams.name=ltm_translations.key) WHERE ltm_translations.key IS NULL LIMIT 40');
		$country_names = DB::select('SELECT countries.name FROM countries LEFT OUTER JOIN ltm_translations ON (countries.name=ltm_translations.key) WHERE ltm_translations.key IS NULL LIMIT 40');
		$league_names = DB::select('SELECT leagues.name FROM leagues LEFT OUTER JOIN ltm_translations ON (leagues.name=ltm_translations.key) WHERE ltm_translations.key IS NULL LIMIT 40');
		$insert_array	=	[];
		if($team_names) {
			foreach ($team_names as  $value) {
				$each_translation 	=	[];
				$each_translation['locale']		=	'en';
				$each_translation['group']		=	'team';
				$each_translation['key']		=	$value;
				$each_translation['value']		=	$value;
				$each_translation['created_at']	=	now();
				$insert_array[]					=	$each_translation;
			}
		}
		if($country_names) {
			foreach ($country_names as  $value) {
				$each_translation 	=	[];
				$each_translation['locale']		=	'en';
				$each_translation['group']		=	'country';
				$each_translation['key']		=	$value;
				$each_translation['value']		=	$value;
				$each_translation['created_at']	=	now();
				$insert_array[]					=	$each_translation;
			}
		}
		if($league_names) {
			foreach ($league_names as  $value) {
				$each_translation 	=	[];
				$each_translation['locale']		=	'en';
				$each_translation['group']		=	'league';
				$each_translation['key']		=	$value;
				$each_translation['value']		=	$value;
				$each_translation['created_at']	=	now();
				$insert_array[]					=	$each_translation;
			}
		}
		foreach (array_chunk($insert_array,1000) as $rows) {
			//saved to database
			LtmTranslation::insert($rows);
		}
		$groups	=	LtmTranslation::select('group')->distinct()->get();
		foreach ($groups as $key => $value) {
			Artisan::call('translations:export',['group' => $value->group]);
		}
    }
}
