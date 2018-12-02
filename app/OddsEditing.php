<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OddsEditing extends Model
{
    protected $guarded	= [];

    public function user()
    {
    	return $this->belongsTo('App\User');
    }
}
