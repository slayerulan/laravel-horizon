<?php

namespace App\Console\Commands\Feed;

use Illuminate\Console\Command;
use App\Http\Controllers\Feed\XmlFeed;

class CountryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feed:fetch-countries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will store new countries';

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
		$this->info('country fetching ..');
        $object = new XmlFeed();
		$object->fetchCountries();
		$this->info('country saved');
    }
}
