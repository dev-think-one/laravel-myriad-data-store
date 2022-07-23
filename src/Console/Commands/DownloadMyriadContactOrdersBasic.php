<?php

namespace MyriadDataStore\Console\Commands;

use Illuminate\Console\Command;

class DownloadMyriadContactOrdersBasic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'myriad-download:contact-orders-basic
    {id : Myriad Contact Id}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download/Update myriad contact\'s orders';

    public function handle()
    {
        $myriadContactID = (int) $this->argument('id');

        $downloaded = (new \MyriadDataStore\Actions\DownloadMyriadContactOrdersBasic())
            ->execute($myriadContactID);

        $this->info("Successful downloaded: {$downloaded} orders.");

        return 0;
    }
}
