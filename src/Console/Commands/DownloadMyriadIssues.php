<?php

namespace MyriadDataStore\Console\Commands;

use Illuminate\Console\Command;

class DownloadMyriadIssues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'myriad-download:issues
    {title_id? : Title ID}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download/Update myriad issues';

    public function handle()
    {
        $titleID = (int) $this->argument('title_id');

        $downloaded = (new \MyriadDataStore\Actions\DownloadMyriadIssues())
            ->execute($titleID ?: null);

        $this->info("Successful downloaded: {$downloaded} issues.");

        return 0;
    }
}
