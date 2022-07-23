<?php

namespace MyriadDataStore\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use MyriadDataStore\MyriadDataDownloader;

class DownloadAllMyriadOrdersBasic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'myriad-download:all-orders-basic
    {--queue=default : Queue name}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download/Update myriad orders for all contacts';

    public function handle()
    {
        $count      = 125;
        $modelClass = MyriadDataDownloader::$contactModel;
        $lastID     = $modelClass::latest()->first()->getKey();
        $queue      = (string) $this->option('queue');

        for ($i = 0; $i < floor($lastID / $count); $i++) {
            $st = ($i * $count) + 1;
            Artisan::call("myriad-download:contacts-orders-basic {$st} --count={$count} --queue={$queue}");
        }
        Artisan::queue('myriad-download:set-issues-dates-for-packages')
               ->onQueue($queue)
               ->delay(Carbon::now()->addMinutes(10));

        return 0;
    }
}
