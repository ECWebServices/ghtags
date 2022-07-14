<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\DB;
use LaravelZero\Framework\Commands\Command;

class UpdateRepo extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'repo:update {name : The name of the project}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Update a project\'s info';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->argument('name');

        // Get the project from the database
        $project = DB::table('repos')->where('name', $this->argument('name'))->first();

        // If there is no project with that name, abort
        if($project == null) {
            $this->error('There is no project with that name');
            return;
        }

        // Ask the user if they want to update the name, repo, and store info
        $updateName = $this->ask('Update the name? (y/n)', 'n');
        $updateRepo = $this->ask('Update the repo? (y/n)', 'n');
        $updateStore = $this->ask('Update the store info? (y/n)' , 'n');

        // If the user wants to update the name, update it
        if($updateName == 'y') {
            $project->name = $this->ask('What is the new name?', $project->name);
        }

        // If the user wants to update the repo, update it
        if($updateRepo == 'y') {
            $project->repo = $this->ask('What is the new repo (e.g. laravel/laravel)?', $project->repo);
        }

        // If the user wants to update the store info, update it
        if($updateStore == 'y') {
            $project->store = $this->ask('What is the store type (e.g. "env", "json"?', $project->store_type);
            $project->store_location = $this->ask('What is the new store location (e.g. /var/www/html/laravel)?', $project->store_location);
            $project->store_file = $this->ask('What is the new store file (e.g. index.html)?', $project->store_file);
            $store_id = $this->ask('What is the new store id (e.g. LATEST_VERSION)?', $project->store_id);
        }
        else {
            $store_id = $project->store_id;
        }

        // Save the project to the database

        DB::table('repos')->where('id', $project->id)->update([
            'name' => $project->name,
            'repo' => $project->repo,
            'store' => $project->store,
            'store_location' => $project->store_location,
            'store_file' => $project->store_file,
            'store_id' => $store_id,
        ]);

        $this->info('Project updated');

        // Ask if the user wants to update the tags
        $updateTags = $this->ask('Update the tags? (y/n)', 'n');

        // If the user wants to update the tags, update them
        if($updateTags == 'y') {
            $this->info('Updating tags...');
            $this->call('tags:refresh', ['--force' => true]);
            $this->call('sync:all');
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
