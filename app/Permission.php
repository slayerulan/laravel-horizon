<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Permission;

class Permission extends Model
{
    public function module()
    {
        //return $this->hasMany('module');
        //return Permission::where('can_view', '=', 1)->get();
        return $this->belongsTo('App\Module');
    }
}
