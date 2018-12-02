<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class UserWallet extends Model
{
    use SoftDeletes;

	protected $guarded	=	[];
	public function transaction()
	{
		return $this->hasMany('App\Transaction');
	}

}
