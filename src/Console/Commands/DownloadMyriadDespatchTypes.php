<?php

namespace MyriadDataStore\Console\Commands;

use Illuminate\Console\Command;

class DownloadMyriadDespatchTypes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'myriad-download:despatch-types';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download/Update myriad despatch types';

    public function handle()
    {
        $downloaded = (new \MyriadDataStore\Actions\DownloadMyriadDespatchTypes())->execute();

        $this->info("Successful downloaded: {$downloaded} despatch types.");

        return 0;
    }
}
