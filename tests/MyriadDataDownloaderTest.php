<?php

namespace MyriadDataStore\Tests;

use MyriadDataStore\MyriadDataDownloader;
use MyriadDataStore\Tests\Fixtures\Models\User;

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

    /** @test */
    public function use_contact_model()
    {
        $this->assertEquals(\MyriadDataStore\Models\MyriadContact::class, MyriadDataDownloader::$contactModel);

        $downloader = MyriadDataDownloader::useContactModel(User::class);
        $this->assertInstanceOf(MyriadDataDownloader::class, $downloader);
        $this->assertEquals(User::class, MyriadDataDownloader::$contactModel);
    }
}
