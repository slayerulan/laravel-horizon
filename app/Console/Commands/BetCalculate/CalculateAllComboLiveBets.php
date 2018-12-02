<?php

namespace App\Console\Commands\BetCalculate;

use Illuminate\Console\Command;
use App\Http\Controllers\BetCalculations\LiveBetCalculation;
use App\FeedModel\Sport;
/**
 * This will run every five minutes with cron to calculate the combo bets for each sport
 *
 * @author Arijit Jana
 */
class CalculateAllComboLiveBets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calculate:combo-live-bet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate the live Combo Bets and store the result';

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
        $this->info('Live Bet Calculation initialize ....');
        // $sport = Sport::select('name')->where('id', $this->argument('sports'))->first();
        $className = 'App\Http\Controllers\BetCalculations\LiveFootballCalculation';
        $object = new $className();
        $object->comboBetCalculation('Football');
        $this->info('Live Bet Calculation sent to queue.');
    }
}
