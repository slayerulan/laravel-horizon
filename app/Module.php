<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Module;

class Module extends Model
{
	protected $guarded	= [];
	
    public function permission()
   {
       return $this->hasMany('App\Permission');
   }
}
