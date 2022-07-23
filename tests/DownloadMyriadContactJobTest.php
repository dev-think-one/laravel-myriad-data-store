<?php

namespace MyriadDataStore\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use MyriadDataStore\Actions\DownloadMyriadContact;
use MyriadDataStore\Jobs\DownloadMyriadContacts;
use MyriadSoap\MyriadSoapException;

class DownloadMyriadContactJobTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function error_start_argument()
    {
        $this->expectExceptionMessage('Invalid start value [0]');
        DownloadMyriadContacts::dispatch(0);
    }

    /** @test */
    public function error_count_argument()
    {
        $this->expectExceptionMessage('Invalid count value [100001]');
        DownloadMyriadContacts::dispatch(1, 100001);
    }

    /** @test */
    public function successCallAction()
    {
        $logSpy                            = Log::spy();
        DownloadMyriadContacts::$logErrors = 'warning';
        $mock                              = \Mockery::mock(DownloadMyriadContact::class);

        $mock->shouldReceive('execute')->with(6);
        $mock->shouldReceive('execute')->with(7)->andThrowExceptions([new MyriadSoapException('Foo Bar')]);

        (new DownloadMyriadContacts(6, 2))->handle($mock);

        $logSpy->shouldHaveReceived('log', ['warning', 'Contact [7] error: Foo Bar']);
    }
}
