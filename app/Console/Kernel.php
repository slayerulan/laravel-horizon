<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\FeedModel\Sport;
use App\Jobs\UpdateTranslationJob;
use App\Jobs\FetchMatchJob;
use App\Jobs\FetchOddsJob;
use App\Jobs\ReturnBetAmountJob;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\CrudCommand',
        'App\Console\Commands\CrudControllerCommand',
        'App\Console\Commands\Feed\TeamCommand',
        'App\Console\Commands\Feed\LeagueCommand',
        'App\Console\Commands\Feed\OddsCommand',
        'App\Console\Commands\Feed\MatchesCommand',
        'App\Console\Commands\Feed\CountryCommand',
        'App\Console\Commands\Feed\LiveFeedCommand',
        'App\Console\Commands\BetCalculate\CalculateAllSingleBets',
        'App\Console\Commands\BetCalculate\CalculateAllComboBets',
        'App\Console\Commands\BetCalculate\CalculateMixedComboBets',
        'App\Console\Commands\BetCalculate\CalculateAllSingleLiveBets',
        'App\Console\Commands\BetCalculate\CalculateAllComboLiveBets',
        'App\Console\Commands\Api\ReturnBetAmount',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
		$all_sports	=	Sport::select('id')->where('status','active')->where('fetch_feed','yes')->get();
		if(!empty($all_sports)){
			foreach ($all_sports as  $sport) {
				$schedule->command('feed:fetch-team '.$sport->id)->everyThirtyMinutes();
				$schedule->command('feed:fetch-league '.$sport->id)->dailyAt('04:00');
			}
		}
		$schedule->command('feed:fetch-countries')->dailyAt('02:00');
		// $schedule->command('feed:fetch-match')->everyFiveMinutes()->withoutOverlapping();
		// $schedule->command('feed:fetch-odds')->everyFiveMinutes()->withoutOverlapping();
		$schedule->command('horizon:snapshot')->everyFiveMinutes();
		// $schedule->job(new UpdateTranslationJob)->daily();
        $schedule->job(new FetchMatchJob)->everyFiveMinutes();
        $schedule->job(new FetchOddsJob)->everyFiveMinutes();
        $schedule->job(new ReturnBetAmountJob)->everyFifteenMinutes();
        // $schedule->command('feed:fetch-livefeed')->everyMinute();

        /** Crons to calculate bets */
        if(!empty($all_sports)){
            foreach ($all_sports as  $sport) {
                $schedule->command('calculate:single-bet '.$sport->id)->everyTenMinutes();
                $schedule->command('calculate:combo-bet '.$sport->id)->everyTenMinutes();
            }
        }
        $schedule->command('calculate:mixcombo-bet')->everyFiveMinutes();
        // $schedule->command('calculate:single-live-bet')->everyFiveMinutes();
        // $schedule->command('calculate:combo-live-bet')->everyFiveMinutes();
        /** Crons to calculate bets */
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
