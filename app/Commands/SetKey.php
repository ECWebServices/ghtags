<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class SetKey extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'set:key';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Add your new Personal Access Token to the database';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $key = $this->ask('Enter your new Personal Access Token');

        // If the key is empty, abort
        if($key == '') {
            $this->error('You must enter a Personal Access Token');
            return;
        }

        // If the key is already in the database, abort
        if(DB::table('key')->where('key', $key)->first() != null) {
            $this->error('That key is already in the database');
            return;
        }

        // Add the key to the database
        DB::table('key')->insert(['key' => $key]);

        $this->info('Key added');
        return;
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
