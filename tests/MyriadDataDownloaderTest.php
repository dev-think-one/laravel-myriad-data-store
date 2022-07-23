<?php

namespace MyriadDataStore\Tests;

use MyriadDataStore\MyriadDataDownloader;

class MyriadDataDownloaderTest extends TestCase
{
    /** @test */
    public function runs_migrations()
    {
        $this->assertTrue(MyriadDataDownloader::$runsMigrations);
        MyriadDataDownloader::ignoreMigrations();
        $this->assertFalse(MyriadDataDownloader::$runsMigrations);
        MyriadDataDownloader::$runsMigrations = true;
    }
}
