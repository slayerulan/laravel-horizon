<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Events\EventSendAccountActivationMail;
class User extends Model
{
	use SoftDeletes,Notifiable;

	protected $guarded	= [];

	// protected $dispatchesEvents = [
 //       'created' => EventSendAccountActivationMail::class,
 //   	];

    public function user_profile()
    {
    	return $this->hasOne('App\UserProfile');
    }
    public function bet_rule()
    {
    	return $this->belongsTo('App\BetRule');
    }
	public function parent()
	{
	    return $this->belongsTo(self::class, 'agent_id');
	}
	public function odds_editing()
	{
	    return $this->hasMany('App\OddsEditing');
	}
	public function children()
	{
	    return $this->hasMany(self::class, 'agent_id');
	}
	public function wallets()
	{
	    return $this->hasMany('App\UserWallet');
	}
	public function betSlips()
	{
	    return $this->hasMany('App\Betslip');
	}
	public function comboBetSlips()
	{
	    return $this->hasMany('App\ComboBetslip');
	}
	/**
	 * Gets the bet rule for a user.
	 *
	 * @return     array  	A array that contains all the bet rules for a user.
	 */
	public function getBetRule()
	{
		// $bet_rules =  $this->bet_rule ? $this->bet_rule : $this->parent->bet_rule;
		// $bet_rules = $bet_rules ? $bet_rules : \App\BetRule::whereNull('user_id')->first();
		// return json_decode($bet_rules->rule);

		if($this->bet_rule) {
			$bet_rules = $this->bet_rule;
		}
		elseif(isset($this->parent->bet_rule)) {
			$bet_rules = $this->parent->bet_rule;
		}
		$bet_rules = isset($bet_rules) ? $bet_rules : \App\BetRule::where('is_default', 'yes')->first();
		return json_decode($bet_rules->rule);
	}
	public function getWallet($id=1)
	{
		return $this->wallets()->where('wallet_id', $id)->first();
	}
	/**
	 * Gets the odds juice value for a user.
	 *
	 * @param      string  		$type      		Bookmaker / Sport.
	 * @param      integer  	$sport_id  		The sport identifier.
	 *
	 * @return     array  		The odds juice.
	 */
	public function getOddsEditing($type, $sport_id=null) {
		if ($type == "Sport") {
			return $this->parent->odds_editing()->where('type', $type)->where('sport_id', $sport_id)->where('status', 'active')->get();
			// return $this->odds_editing()->where('type', $type)->where('sport_id', $sport_id)->where('status', 'active')->get();
		}
		else{
			return $this->parent->odds_editing()->where('type', $type)->where('status', 'active')->get();
			// return $this->odds_editing()->where('type', $type)->where('status', 'active')->get();
		}
	}
}
