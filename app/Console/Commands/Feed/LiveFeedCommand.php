<?php

namespace App\Console\Commands\Feed;

use Illuminate\Console\Command;
use App\Http\Controllers\Feed\LiveFeed;
/**
 * This will run every minutes to fetch the live feed and save in the database
 *
 * @author Arijit Jana
 */
class LiveFeedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feed:fetch-livefeed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will fetch & save all the data from live feed.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('live feed job initialize ....');
        $object = new LiveFeed();
        $object->fetchLiveMatch();
		$this->info('live feed job sent to queue.');
    }
}
