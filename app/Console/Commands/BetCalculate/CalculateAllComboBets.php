<?php

namespace App\Console\Commands\BetCalculate;

use Illuminate\Console\Command;
use App\Http\Controllers\BetCalculations\BetCalculation;
use App\FeedModel\Sport;
/**
 * This will run every five minutes with cron to calculate the combo bets for each sport
 *
 * @author Arijit Jana
 */
class CalculateAllComboBets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calculate:combo-bet {sports}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate the Combo Bets and store the result';

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
        $this->info('Bet Calculation initialize ....');
        $sport = Sport::select('name')->where('id', $this->argument('sports'))->first();
        $className = 'App\Http\Controllers\BetCalculations'.'\\'.str_replace(" ","",$sport->name).'Calculation';
        $object = new $className();
        $object->comboBetCalculation($sport->name);
        $this->info('Bet Calculation sent to queue.');
    }
}
