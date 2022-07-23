<?php

namespace MyriadDataStore\Console\Commands;

use Illuminate\Console\Command;

class DownloadMyriadTitles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'myriad-download:titles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download/Update myriad titles';

    public function handle()
    {
        $downloaded = (new \MyriadDataStore\Actions\DownloadMyriadTitles())->execute();

        $this->info("Successful downloaded: {$downloaded} titles.");

        return 0;
    }
}
