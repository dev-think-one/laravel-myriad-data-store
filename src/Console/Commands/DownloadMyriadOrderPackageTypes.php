<?php

namespace MyriadDataStore\Console\Commands;

use Illuminate\Console\Command;

class DownloadMyriadOrderPackageTypes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'myriad-download:order-package-types';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download/Update myriad order package types';

    public function handle()
    {
        $downloaded = (new \MyriadDataStore\Actions\DownloadMyriadOrderPackageTypes())->execute();

        $this->info("Successful downloaded: {$downloaded} order package types.");

        return 0;
    }
}
