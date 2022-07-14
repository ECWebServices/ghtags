<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\DB;
use LaravelZero\Framework\Commands\Command;

class SyncSingleTag extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'sync:single {name : The name of the project to sync}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Sync a projects latest tag from the database to it\'s file';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');

        // Get the project from the database
        $project = DB::table('repos')->where('name', $name)->first();

        // If there is no project with that name, abort
        if($project == null) {
            $this->error('There is no project with that name');
            return;
        }

        $this->info('Syncing ' . $project->name . '...');
        $this->info("\n");

        // Get the latest tag from the database
        $tag = DB::table('tags')->where('repo_id', $project->id)->orderBy('id', 'desc')->first();

        // If there are no tags in the database, abort
        if($tag == null) {
            $this->error('There are no tags in the database');
            return;
        }
        // Check if the store file exists
        if(!file_exists($project->store_location . '/' . $project->store_file)) {
            // If it doesn't, create it
            $this->info('Creating store file...');

            touch($project->store_location . '/' . $project->store_file);
        }

        if($project->store == 'env') {
            // Set the tag in the .env file
            $this->info('Setting tag in .env file');
            $this->info('Setting ' . $project->store_id . ' to ' . $tag->tag);
            $env = file_get_contents($project->store_location . '/' . $project->store_file);
            // check if the key is already set
            if(strpos($env, $project->store_id !== false)) {
                // key is already set, replace it
                $env = preg_replace('/' . $project->store_id . '=.*/', $project->store_id . '=' . $tag->tag, $env);
            } else {
                // key is not set, add it
                $env .= "\n" . $project->store_id . '=' . $tag->tag;
            }

            file_put_contents($project->store_location . '/' . $project->store_file, $env);
        } else if($project->store == 'json') {
            // Set the tag in the json file
//            $this->info('Setting tag in json file');
//            $this->info('Setting ' . $project->json_key . ' to ' . $tag->tag);
//            $json = file_get_contents(base_path($project->json_path));
//            $json = preg_replace('/' . $project->json_key . '=.*/', $project->json_key . '=' . $tag->tag, $json);
//            file_put_contents(base_path($project->json_path), $json);
            $this->info('JSON support is not yet implemented');
        } else {
            $this->error('Unknown store type');
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
