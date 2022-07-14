<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use LaravelZero\Framework\Commands\Command;

class Update extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'update
                            {--sync : Sync the tags to their respective files}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Updates the tags in the database, and updates the tags in the repositories';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Get the latest keys from the database
        $key = DB::table('key')->orderBy('id', 'desc')->first();

        // If there are no keys in the database, abort
        if (!$key) {
            $this->info("\n\n");
            $this->info('No keys in the database. Aborting...');
            $this->info('Please run "setup" to add your first Key to the database..');
            $this->info("\n\n");
            return;
        }

        // Get the repositories from the database
        $repos = DB::table('repos')->get();

        // If there are no repositories, abort
        if (count($repos) == 0) {
            $this->info("\n\n");
            $this->info('There are no repositories in the database.');
            $this->info("\n\n");
            return;
        }

        // Loop through the repositories
        foreach ($repos as $repo) {
            // Get the tags about this repository
            $tags = DB::table('tags')->where('repo_id', $repo->id)->get();
            $this->info("\n\n");
            $this->info("Updating tags for $repo->name...");
            $getTags = Http::withHeaders(['Authorization' => "token $key->key", 'Accept' => 'application/vnd.github+json'])->get("https://api.github.com/repos/" . $repo->repo . "/tags",
            [
                'per_page' => 1,
            ]);
            $tags = $getTags->json();
            // If there are no tags, abort
            if (count($tags) == 0) {
                $this->info("\n\n");
                $this->info('There are no tags in this repository.');
                $this->info("\n\n");
                continue;
            }


            // Get the count of tags in the database
            $count = DB::table('tags')->where('repo_id', $repo->id)->count();

            // Loop through the tags and add to the database if they aren't already there
            foreach ($tags as $tag) {
                $tag_exists = DB::table('tags')->where('repo_id', $repo->id)->where('tag', $tag['name'])->first();
                if (!$tag_exists) {
                    $this->info('Adding tag ' . $tag['name'] . ' to the database...');
                    DB::table('tags')->insert(['repo_id' => $repo->id, 'tag' => $tag['name']]);
                }
            }

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
