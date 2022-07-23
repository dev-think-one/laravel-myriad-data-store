<?php

namespace MyriadDataStore\Console\Commands;

use Illuminate\Console\Command;

class DownloadMyriadProductTypes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'myriad-download:product-types';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download/Update myriad product types';

    public function handle()
    {
        $downloaded = (new \MyriadDataStore\Actions\DownloadMyriadProductTypes())->execute();

        $this->info("Successful downloaded: {$downloaded} product types.");

        return 0;
    }
}
