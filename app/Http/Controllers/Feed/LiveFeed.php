<?php

namespace App\Http\Controllers\Feed;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Traits\Feed\LiveMatchFeed;
use App\SystemActivityLog;
/**
 * fetches the live matches from the feed and saves it into the database
 *
 * @author Arijit Jana
 */
class LiveFeed extends Controller
{
    use LiveMatchFeed;
	
	public function __construct(){
		ini_set('max_execution_time', 0);
		ini_set('memory_limit','1000M');
	}
}
