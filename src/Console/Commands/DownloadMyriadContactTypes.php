<?php

namespace MyriadDataStore\Console\Commands;

use Illuminate\Console\Command;

class DownloadMyriadContactTypes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'myriad-download:contact-types';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download/Update myriad contact types';

    public function handle()
    {
        $downloaded = (new \MyriadDataStore\Actions\DownloadMyriadContactTypes())->execute();

        $this->info("Successful downloaded: {$downloaded} contact types.");

        return 0;
    }
}
