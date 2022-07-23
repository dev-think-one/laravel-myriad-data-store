<?php

namespace MyriadDataStore\Console\Commands;

use Illuminate\Console\Command;
use MyriadDataStore\MyriadDataDownloader;
use MyriadSoap\MyriadSoapException;

class DownloadLatestMyriadContacts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'myriad-download:contacts:latest
    {start? : Start contact ID, if empty - will be used latest from database}
    {--buffer=10 : Buffer items count}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download/Update latest myriad contacts';

    public function handle()
    {
        $start = (int) $this->argument('start');
        if ($start <= 0) {
            $modelClass = MyriadDataDownloader::$contactModel;
            $start      = $modelClass::select(['id'])->orderBy('id', 'DESC')->first()?->id ?: 0;
            $start += 1;
        }
        $buffer = (int) $this->option('buffer');
        if ($buffer < 0 || $buffer > 500) {
            $this->error("Invalid buffer value [$buffer]");

            return 1;
        }

        $successDownloads = 0;
        $errors           = 0;
        for ($i = $start; $i <= ($start + 1000); $i++) {
            try {
                $myriadContact = (new \MyriadDataStore\Actions\DownloadMyriadContact())
                    ->execute($i);
                if ($myriadContact) {
                    $successDownloads++;
                    (new \MyriadDataStore\Actions\DownloadMyriadContactOrdersBasic())
                        ->execute($i);
                }
            } catch (MyriadSoapException $e) {
                $errors++;
            }

            if ($errors >= $buffer) {
                break;
            }
        }

        $this->info("Successful downloaded: {$successDownloads} contacts.");

        return 0;
    }
}
