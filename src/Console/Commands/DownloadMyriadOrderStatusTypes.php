<?php

namespace MyriadDataStore\Console\Commands;

use Illuminate\Console\Command;

class DownloadMyriadOrderStatusTypes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'myriad-download:order-status-types';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download/Update myriad order status types';

    public function handle()
    {
        $downloaded = (new \MyriadDataStore\Actions\DownloadMyriadOrderStatusTypes())->execute();

        $this->info("Successful downloaded: {$downloaded} order status types.");

        return 0;
    }
}
