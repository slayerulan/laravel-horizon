<?php

namespace App\Console\Commands\Feed;

use Illuminate\Console\Command;
use App\Jobs\FetchMatchJob;
use App\Http\Controllers\Feed\XmlFeed;


/**
 *  This is a command to fetch all match details. It will dispatch a job and send to queue
 *  @author	Anirban Saha
 */
class MatchesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feed:fetch-matches';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will fetch & save matches value for specific sport.';

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
		$this->info('matches job initialize ..');
		$object = new XmlFeed();
		$object->fetchMatches();
		FetchMatchJob::dispatch()->onQueue('XmlFeed');
		$this->info('matches job sent to queue.');
    }
}
