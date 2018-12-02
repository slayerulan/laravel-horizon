<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Http\Controllers\Feed\XmlFeed;

/**
 *  this is a job, dispatch by command to store match into database.
 *   Actually it is using XmlFeed Controller. fetchMatches method is defined in trait.
 *
 *  @author	Anirban Saha
 */

class FetchMatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $object = new XmlFeed();
		$object->fetchMatches();
    }

	public function tags()
    {
		return ['matches', 'sport : All'];
    }
}
