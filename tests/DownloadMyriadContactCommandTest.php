<?php

namespace MyriadDataStore\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use MyriadDataStore\MyriadDataDownloader;
use MyriadSoap\MyriadSoap;

class DownloadMyriadContactCommandTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function download_contact()
    {
        /** @var \Mockery\Mock $mock */
        $mock = MyriadSoap::mockClient();

        $mock->shouldReceive('__soapCall')
             ->with('SOAP_getContactDetails', \Hamcrest\Core\IsTypeOf::typeOf('array'))
             ->andReturn([
                 'Contact_ID' => 22,
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

        Artisan::call('myriad-download:contact 22');

        $class         = MyriadDataDownloader::$contactModel;
        $myriadContact = $class::find(22);
        $this->assertInstanceOf($class, $myriadContact);
        $this->assertEquals('foo.tr@pg.com', $myriadContact->email);
        $this->assertCount(4, $myriadContact->communications->getRawData());
        $this->assertEquals('Peterson', $myriadContact->details->getAttribute('Forename'));
    }
}
