<?php

namespace App\Console\Commands\Feed;

use Illuminate\Console\Command;
use App\Jobs\FetchOddsJob;
use App\Http\Controllers\Feed\XmlFeed;


/**
 *  this is a command to fetch all odds. It will dispatch a job and send to queue
 *
 *  @author	Anirban Saha
 */
class OddsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feed:fetch-odds';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will fetch & save odds value for all sport.';

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
		$this->info('odds job initialize ..');
		$object = new XmlFeed();
		$object->fetchOdds();

		FetchOddsJob::dispatch()->onQueue('XmlFeed');
		$this->info('odds job sent to queue.');
    }
}
