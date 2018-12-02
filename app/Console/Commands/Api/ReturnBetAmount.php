<?php

namespace App\Console\Commands\Api;

use Illuminate\Console\Command;
use App\Jobs\ReturnBetAmountJob;
use App\Http\Controllers\ApiIntegration\ProviderApi;

class ReturnBetAmount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'return:bet-amount';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Returns the amount back to the user if a bet is won, refunded or canceled';

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
        $this->info('Return Bet Amount initialize ....');
        // $object = new ProviderApi();
        // $object->returnBetAmount();
        ReturnBetAmountJob::dispatch()->onQueue('ProviderApi');
        $this->info('Return Bet Amount job sent to queue.');
    }
}
