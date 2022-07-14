<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\DB;
use LaravelZero\Framework\Commands\Command;

class DeleteRepo extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'repo:delete {name : The name of the project} {--force : Force the deletion of the project}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Delete a project';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->argument('name');
        $this->option('force');

        // Get the project from the database
        $project = DB::table('repos')->where('name', $this->argument('name'))->first();

        // If there is no project with that name, abort
        if($project == null) {
            $this->error('There is no project with that name');
            return;
        }

        // If the user doesn't want to force the deletion, ask if they want to delete the project
        if(!$this->option('force')) {
            $delete = $this->ask('Are you sure you want to delete ' . $project->name . '? (y/n)', 'n');
        } else {
            $delete = 'y';
        }

        // If the user wants to delete the project, delete it
        if($delete == 'y') {
            DB::table('repos')->where('name', $this->argument('name'))->delete();
            $this->info('Project deleted');
            // Delete the tags associated with the project
            DB::table('tags')->where('repo_id', $project->id)->delete();
            $this->info('Tags deleted');
            return;
        } else {
            $this->info('Project not deleted');
            return;
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
