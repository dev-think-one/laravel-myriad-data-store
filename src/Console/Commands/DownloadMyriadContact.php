<?php

namespace MyriadDataStore\Console\Commands;

use Illuminate\Console\Command;

class DownloadMyriadContact extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'myriad-download:contact
    {id : Myriad Contact Id}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download/Update myriad contact';

    public function handle()
    {
        $myriadContactID = (int) $this->argument('id');

        (new \MyriadDataStore\Actions\DownloadMyriadContact())
            ->execute($myriadContactID);

        return 0;
    }
}
