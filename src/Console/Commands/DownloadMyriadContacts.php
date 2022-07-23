<?php

namespace MyriadDataStore\Console\Commands;

use Illuminate\Console\Command;
use MyriadSoap\MyriadSoapException;

class DownloadMyriadContacts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'myriad-download:contacts
    {start=1 : Start contact ID}
    {--count=1000 : Count identifiers}
    {--queue=sync : Queue name}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download/Update myriad contact';

    public function handle()
    {
        $start = (int) $this->argument('start');
        if ($start <= 0) {
            $this->error("Invalid start value [$start]");

            return 1;
        }
        $count = (int) $this->option('count');
        if ($count <= 0 || $count > 100000) {
            $this->error("Invalid count value [$count]");

            return 1;
        }
        $queue = (string) $this->option('queue');

        if ($queue === 'sync') {
            $successDownloads = 0;
            $errorDownloads   = 0;

            for ($i = $start; $i < ($start + $count); $i++) {
                try {
                    $myriadContact = (new \MyriadDataStore\Actions\DownloadMyriadContact())
                        ->execute($i);
                    if ($myriadContact) {
                        $successDownloads++;
                    } else {
                        $errorDownloads++;
                    }
                } catch (MyriadSoapException $e) {
                    $this->error("Contact [$i] error: " . $e->getMessage());
                    $errorDownloads++;
                }
            }

            if ($errorDownloads == 0) {
                $this->info("Successful downloaded: {$successDownloads} contacts.");
            } else {
                $this->warn("Successful downloaded: {$successDownloads} contacts.");
                $this->warn("Downloads fails: {$errorDownloads} contacts.");
            }
        } else {
            \MyriadDataStore\Jobs\DownloadMyriadContacts::dispatch($start, $count)->onQueue($queue);
            $this->info("Job sent to queue [$queue].");
        }

        return 0;
    }
}
