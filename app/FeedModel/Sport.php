<?php

namespace App\FeedModel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *  this is sports table model.
 *
 *  @author Anirban Saha
 */
class Sport extends Model
{
	use SoftDeletes;
	/**
	 *  it will use feed default id, so no auto increment
	 *
	 *  @var  bool
	 */
	public $incrementing = false;

	/**
	 *  we will not create any sport, no need to guarded any field.
	 *
	 *  @var  array
	 */
	protected $guarded = [];

	/**
	 *  this will contain current matches collection object.
	 *   set from getcurrentMatches
	 *
	 *  @var  object
	 */
	public $current_matches	=	false;

	/**
	 *  this will contain unique conutry list, id and name in associative array.
	 *  set from setCurrentCountries
	 *
	 *  @var  array
	 */
	public $current_countries	=	false;

	/**
	 *  this will contain unique league list, id and name in associative array.
	 *  set from setCurrentLeagues
	 *
	 *  @var  array
	 */
	public $current_leagues			=	false;

	/**
	 *  this will contain unique top league list, id and name in associative array.
	 *  set from setCurrentTopLeagues
	 *
	 *  @var  array
	 */
	public $current_top_leagues		=	false;

	/**
	 *  this will contain league count for each country
	 *  set from setLeagueCountByCountry
	 *
	 *  @var  array
	 */

	public $league_count_by_country	=	false;

	/**
	 *  this will contain contry name for each league
	 *
	 *  @var  array
	 */
	public $league_with_country		=	[];
	/**
	 *  A sport has many leagues
	 *
	 *  @return  object  $this
	 */
	public function leagues()
	{
		return $this->hasMany('App\FeedModel\League');
	}

	/**
	 *  A sport has many markets
	 *
	 *  @return  object  $this
	 */
	public function markets()
	{
		return $this->hasMany('App\FeedModel\Market');
	}

	/**
	 *  A sport has many leagues, each league has many matches
	 *
	 *  @return  object  $this
	 */
	public function matches()
	{
		return $this->hasManyThrough('App\FeedModel\Match','App\FeedModel\League');
	}

	/**
	 *  each sport has a default market which will be visible in match listing page
	 *
	 *  @return  object  $this
	 */
	public function default_market()
	{
		return $this->belongsTo('App\FeedModel\Market');
	}
	/**
	 *  each sport has betslips
	 *
	 *  @return  object  $this
	 */
	public function betslips()
	{
		return $this->hasMany('App\Betslip');
	}
	/**
	 *  this will return current matches object with league and country details
	 *
	 *  @return  object  all matches collection object
	 */
	public function getcurrentMatches()
	{
		if(count((array)$this->current_matches)) {
			$where	=	[
				// 'bookmaker_id'	=> 	getBetRule()->bookmaker,
				'market_id'		=> 	$this->market_id,
				'is_locked'		=> 	0,
				'sport_id'		=> $this->id
			];

			$this->current_matches	=	$this->matches()
											->where('start_timestamp', '>=',strtotime('+'.config('bet_settings.hide_minute',0).' minutes'))
											->where('start_timestamp', '<=',strtotime('+'.config('bet_settings.maximum_hour',24*7).' hours'))
											->whereHas('odds', function($query) use($where){
												$query->select('id')->where($where)->limit(1);
												})
											->with('league.country')->get();
		}else {
			$this->current_matches 		= new \stdClass();
		}
		return $this->current_matches;
	}

	/**
	 *  this will return unique country details of current matches
	 *
	 *  @return  object  current object,so you can do method chaining
	 */
	public function setCurrentCountries()
	{
		$this->current_countries	= $this->getcurrentMatches()->pluck('league.country.name','league.country_id')->sort();
		return $this;
	}

	/**
	 *  this will return unique league details and respective country details of current matches
	 *
	 *  @param   null|int  $country_id  null for all, or speciific country id
	 *  @param   int  $time_range  maximum starting hour limit
	 *  @return  object  current object,so you can do method chaining
	 */
	public function setCurrentLeagues($country_id,$time_range)
	{
		$collection	= $this->getcurrentMatches()->filter(function ($value, $key) use($country_id,$time_range) {
    			return $value->league->is_top == 'no'
						&& ($country_id == null || $value->league->country_id == $country_id)
						&& ($time_range == null || $value->start_timestamp <= strtotime('+'. $time_range .' hours'));
			});
			$this->current_leagues['league_details'] 	=	$collection->pluck('league.name','league.id');
			$this->current_leagues['league_country']	=	$collection->pluck('league.country.name','league.id')->sort();
		return $this;
	}

	/**
	*  this will set unique TOP league details and respective country details of current matches
	 *
	 *  @param   null|int  $country_id  null for all, or speciific country id
	 *  @param   int  $time_range  maximum starting hour limit
	 *  @return  object  current object,so you can do method chaining
	 */
	public function setCurrentTopLeagues($country_id,$time_range)
	{
		$collection	= $this->getcurrentMatches()->filter(function ($value, $key) use($country_id,$time_range) {
    			return $value->league->is_top == 'yes'
					&& ($country_id == null || $value->league->country_id == $country_id)
					&& ($time_range == null || $value->start_timestamp <= strtotime('+'. $time_range .' hours'));
		});
		$this->current_top_leagues['league_details']	=	$collection->pluck('league.name','league.id');
		$this->current_top_leagues['league_country']	=	$collection->pluck('league.country.name','league.id')->sort();
		return $this;
	}

	/**
	 *  this will set league count for each country
	 *
	 *  @return  object  current object,so you can do method chaining
	 */
	public function setLeagueCountByCountry()
	{
		$this->league_count_by_country	=	$this->getcurrentMatches()->groupBy('league.country_id')->mapToGroups(function ($item, $key) {
			return [$key => $item->unique('league.id')->count()];
		});
		return $this;
	}

	/**
	 *  this will return a compact details for sports, country and league count.
	 *  this array will be usefull to generate left bar in front end.
	 *
	 *  @return  array  country details with league count
	 */
	public function getCountryWithLeagueCount()
	{
		$this->setCurrentCountries()->setLeagueCountByCountry();
		$country_array		=	[];
		foreach ($this->current_countries as $key => $value) {
			$data  				=  new \stdClass();
			$data->country_id	= $key;
			$data->name			= $value;
			$data->league_count	= (string)$this->league_count_by_country[$key][0];
			$country_array[$key] = $data;
		}
		return $country_array;
	}
}
