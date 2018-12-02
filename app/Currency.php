<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
	protected $guarded = [];

    /**
     * Scope a query to only include active users.
     *
     * @param      \Illuminate\Database\Eloquent\Builder  $query  The query
     *
     * @return     \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
