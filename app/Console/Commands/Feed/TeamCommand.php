<?php

namespace App\Console\Commands\Feed;

use Illuminate\Console\Command;
use App\Http\Controllers\Feed\XmlFeed;

/**
 *  this is a command to fetch all team.
 *  it should run in cron everyday for each sport
 *
 *  @author	Anirban Saha
 */

class TeamCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feed:fetch-team {sports_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will fetch & ONLY INSERT team name for specific sport.';

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
		$this->info('team info fetching ..');
        $object = new XmlFeed();
		$object->fetchTeams($this->argument('sports_id'));
		$this->info('team saved');
    }
}
