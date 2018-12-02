<?php

namespace App\Console\Commands\Feed;

use Illuminate\Console\Command;
use App\Http\Controllers\Feed\XmlFeed;

/**
 *  This is a command to fetch all league and save into database.
 *
 *  @author	Anirban Saha
 */
class LeagueCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feed:fetch-league {sports_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will fetch & save league name for specific sport.';

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
		$this->info('league fetching ..');
        $object = new XmlFeed();
		$object->fetchLeague($this->argument('sports_id'));
		$this->info('league saved');
    }
}
