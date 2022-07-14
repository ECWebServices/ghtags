<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use LaravelZero\Framework\Commands\Command;

class InitialSetup extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'setup';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Setup GHTags';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $newDatabase = false;
        $createDatabase = false;
        $getKey = true;

        // Check if the database is already setup

        $this->info('Checking if the database is already setup...');

        // Check if the root directory exists
        if (!file_exists(config('filesystems.ghtags.root'))) {
            $this->info('Creating' . config('filesystems.ghtags.root') . ' directory...');
            mkdir(config('filesystems.ghtags.root'));
        }

        // Check if the database.sqlite file exists in the .ghtags directory
        if (file_exists(config('filesystems.ghtags.root') . '/database.sqlite')) {
            $this->info('Database already setup.');
            if(!Schema::hasTable('repos')) {
                $createDatabase = true;
            }
        } else {
            $this->info('Database not setup.');
            $newDatabase = true;
            $createDatabase = true;
        }

        if ($newDatabase) {
            $this->info('Creating database.sqlite file...');

            touch(config('filesystems.ghtags.root') . '/database.sqlite');
        }

        if($createDatabase) {
            $this->info('Database not found, creating...');
            $this->call('migrate');
        }
        else {
            $this->info('Database found, skipping...');
        }

        // Check if the key is already setup

        $this->info('Checking if the key is already setup...');

        if(DB::table('key')->exists()) {
            $getKey = false;
        }

        if($getKey) {
            $this->info('Key not found, Please add a key...');

            $this->info('Head to https://github.com/settings/tokens/new to create a token');
            $this->info('Please add the "repo" scope to the token');
            $this->info('Then copy the token and paste it below');

            $key = $this->ask('Token:');

            DB::table('key')->insert([
                'key' => $key
            ]);

            $this->info('Key added');
        }
        else {
            $this->info('Key found, skipping...');
        }

        $this->info('Setup complete');

        return 0;
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
