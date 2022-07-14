<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use LaravelZero\Framework\Commands\Command;

class NewRepo extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'repo:new';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Adds a new repository to the list of repositories';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Strtolower name, repo, type, and file.
        $name = strtolower($this->ask('Name of the repository:'));
        $repo = strtolower($this->ask('What is the id of the repo on GitHub (e.g. laravel/laravel):'));
        $type = strtolower($this->ask('Where do you want to store the tags? (e.g. "env", "json")?'));
        $file = strtolower($this->ask('What is the name of the file? (e.g. ".env", "tags.json")?'));
        $id = $this->ask('What is the KEY you want to store the tag as? (e.g. "LATEST_TAG", "mytag")?');


        if($type === 'env') {
            $id = strtoupper($id);
        }
        else {
            $id = strtolower($id);
        }

        $this->info("\n\n");
        $this->info("Name: $name");
        $this->info("Repository: $repo");
        $this->info("Type: $type");
        $this->info("File: $file");
        $this->info("\n\n");
        $this->info("Is this correct? (y/n)");
        $answer = $this->ask('', 'y');
        if ($answer !== 'y') {
            $this->info("\n\n");
            $this->info('Aborting...');
            $this->info("\n\n");
            return;
        }
        $this->info("\n\n");

        $this->info('Checking if repository exists...');

        // Make sure the repository isn't already in the database
        $repo_exists = DB::table('repos')->where('repo', $repo)->first();

        if ($repo_exists) {
            $this->info("\n\n");
            $this->info('Repository already exists in the database.');
            $this->info("\n\n");
            return;
        }

        // Get the latest Key from the Database
        $token = DB::table('keys')->orderBy('id', 'desc')->first();

        // If there are no keys in the database, we can't continue
        if (!$token) {
            $this->info("\n\n");
            $this->info('You have not saved your Personal Access Token. Please run the "setup" command.');
            $this->info("\n\n");
            return;
        }

        // Make sure we can find the repository on GitHub
        $repo_check = Http::withHeaders(['Accept' => 'application/vnd.github.v3+json', 'Authorization' => 'token ' . $token])->get("https://api.github.com/repos/$repo");

        if ($repo_check->status() === 301) {
            $this->info("\n\n");
            $this->info('Repository reponded with Moved Permanantly.');
            $this->info('Aborting...');
            $this->info("\n\n");
            return;
        }
        elseif ($repo_check->status() === 404) {
            $this->info("\n\n");
            $this->info('Repository not found.');
            $this->info('Aborting...');
            $this->info("\n\n");
            return;
        }
        elseif ($repo_check->status() !== 200) {
            $this->info("\n\n");
            $this->info('Repository responded with an error.');
            $this->info('Aborting...');
            $this->info("\n\n");
            return;
        }
        else {
            $this->info("\n\n");
            $this->info('Repository found.');
            $this->info("\n\n");
        }


        // Add the repository to the database
        $this->info('Adding repository...');
        DB::table('repos')->insert([
            'name' => $name,
            'repo' => $repo,
            'store' => $type,
            'store_id' => $id,
            'store_location' => getcwd(),
            'store_file' => $file,
        ]);
        $this->info("\n\n");
        $this->info('Done!');
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
