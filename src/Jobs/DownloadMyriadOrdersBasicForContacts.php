<?php

namespace MyriadDataStore\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use MyriadSoap\MyriadSoapException;

class DownloadMyriadOrdersBasicForContacts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public static bool|string $logErrors = false;

    protected int $startContactId = 1;
    protected int $count          = 1000;

    public function __construct($startContactId = 1, $count = 1000)
    {
        if ($startContactId <= 0) {
            throw new \Exception("Invalid start value [$startContactId]");
        }
        if ($count <= 0 || $count > 100000) {
            throw new \Exception("Invalid count value [$count]");
        }

        $this->startContactId = $startContactId;
        $this->count          = $count;
    }

    public function handle(\MyriadDataStore\Actions\DownloadMyriadContactOrdersBasic $action)
    {
        for ($i = $this->startContactId; $i < ($this->startContactId + $this->count); $i++) {
            try {
                $action->execute($i);
            } catch (MyriadSoapException $e) {
                if (static::$logErrors) {
                    Log::log(
                        is_string(static::$logErrors) ? static::$logErrors : 'error',
                        "Contact [$i] orders download error: ".$e->getMessage()
                    );
                }
            }
        }
    }
}
