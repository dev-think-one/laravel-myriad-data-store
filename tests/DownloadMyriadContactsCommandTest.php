<?php

namespace MyriadDataStore\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use MyriadDataStore\Jobs\DownloadMyriadContacts;
use MyriadDataStore\MyriadDataDownloader;
use MyriadSoap\MyriadSoap;

class DownloadMyriadContactsCommandTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function download_contacts()
    {
        /** @var \Mockery\Mock $mock */
        $mock = MyriadSoap::mockClient();

        $mock->shouldReceive('__soapCall')
             ->with('SOAP_getContactDetails', \Hamcrest\Core\IsTypeOf::typeOf('array'))
             ->andReturn([
                 'Contact_ID' => 564,
                 'Forename'   => 'Peterson',
             ]);

        $mock->shouldReceive('__soapCall')
             ->with('SOAP_getContactCommunications', \Hamcrest\Core\IsTypeOf::typeOf('array'))
             ->andReturn(
                 [
                     'ContactCommunication' => [
                         '410839;12;01279 506193;No',
                         '410840;14;bar.tr@pg.com;No',
                         '410841;14;foo.tr@pg.com;Yes',
                         '410842;12;07825 978 907;Yes',
                     ],
                 ]
             );

        Artisan::call('myriad-download:contacts 564 --count=1');

        $class         = MyriadDataDownloader::$contactModel;
        $myriadContact = $class::find(564);
        $this->assertInstanceOf($class, $myriadContact);
        $this->assertEquals('foo.tr@pg.com', $myriadContact->email);
        $this->assertCount(4, $myriadContact->communications->getRawData());
        $this->assertEquals('Peterson', $myriadContact->details->getAttribute('Forename'));
    }

    /** @test */
    public function download_in_queue_contacts()
    {
        Queue::fake();

        Queue::assertNothingPushed();

        $this->artisan('myriad-download:contacts 564 --count=1 --queue=foo')
        ->assertSuccessful()
        ->expectsOutput('Job sent to queue [foo].');

        Queue::assertPushedOn('foo', DownloadMyriadContacts::class);
    }

    /** @test */
    public function error_response()
    {
        /** @var \Mockery\Mock $mock */
        $mock = MyriadSoap::mockClient();

        $mock->shouldReceive('__soapCall')
             ->with('SOAP_getContactDetails', \Hamcrest\Core\IsTypeOf::typeOf('array'))
            ->andReturn([ 'faultcode' => 'faultcodeTest', 'faultstring' => 'faultstringTest' ]);
        ;

        $this->artisan('myriad-download:contacts 564 --count=1')
        ->assertSuccessful()
        ->expectsOutput('Successful downloaded: 0 contacts.')
        ->expectsOutput('Downloads fails: 1 contacts.');

        $myriadContact = MyriadDataDownloader::$contactModel::find(564);
        $this->assertNull($myriadContact);
    }

    /** @test */
    public function error_arguments()
    {
        $this->artisan('myriad-download:contacts 0 --count=1')
        ->assertExitCode(1)
        ->expectsOutput('Invalid start value [0]');

        $this->artisan('myriad-download:contacts --count=100001')
        ->assertExitCode(1)
        ->expectsOutput('Invalid count value [100001]');

        $this->artisan('myriad-download:contacts --count=0')
        ->assertExitCode(1)
        ->expectsOutput('Invalid count value [0]');
    }
}
