<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\DB;
use LaravelZero\Framework\Commands\Command;

class SyncTags extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'sync:all';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Syncs all tags from the database to their files';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Get all the repositories from the database
        $repos = DB::table('repos')->get();

        // If there are no repositories, abort
        if (count($repos) == 0) {
            $this->info("\n\n");
            $this->info('There are no repositories in the database.');
            $this->info("\n\n");
            return;
        }

        // Loop through the repositories and sync their tags
        foreach ($repos as $repo) {
            $this->call('sync:single', ['name' => $repo->name]);
        }
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
