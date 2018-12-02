<?php

namespace App\Console\Commands\BetCalculate;

use Illuminate\Console\Command;
use App\Http\Controllers\BetCalculations\MixComboCalculation;
/**
 * This will run every five minutes with cron to calculate the mix combo bets
 *
 * @author Arijit Jana
 */
class CalculateMixedComboBets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calculate:mixcombo-bet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate the Mix Combo Bets and store the result';

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
        $this->info('Mix Combo Bet Calculation initialize ....');
        $object = new MixComboCalculation();
        $object->index();
        $this->info('Mix Combo Bet Calculation sent to queue.');
    }
}
