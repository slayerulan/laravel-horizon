<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ComboBetslip extends Model
{
    protected $guarded	=	[];

	public function combinations()
	{
		return $this->hasMany('App\ComboBetslipCombination');
	}

	public function sport()
  {
      return $this->belongsTo('App\FeedModel\Sport');
  }

  public function user()
  {
      return $this->belongsTo('App\User');
  }

  public function match()
  {
      return $this->belongsTo('App\FeedModel\Match','match_id','match_id');
  }
}
