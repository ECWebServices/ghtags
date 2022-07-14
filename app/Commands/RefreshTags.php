<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\DB;
use LaravelZero\Framework\Commands\Command;

class RefreshTags extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'tags:refresh {--force : Force the refresh}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Removes all tags from the database and updates them from the remote repository';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
       $run = strtolower($this->ask('Are you sure you want to refresh the tags? [yes, no]', 'no'));

       if($this->option('force')) {
              $run = 'yes';
       }

       if ($run == 'no') {
              $this->info("\n\n");
              $this->info('Aborting.');
              $this->info("\n\n");
              return;
       }
       elseif ($run != 'yes') {
              $this->info("\n\n");
              $this->info('Please enter either "yes" or "no".');
              $this->info("\n\n");
              return;
       }

       // Delete all tags from the database
         DB::table('tags')->delete();

       $this->info("\n\n");
         $this->info('Deleted all tags from the database.');
         $this->info("\n\n");

       // Run the update command
         $this->call('update');

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
