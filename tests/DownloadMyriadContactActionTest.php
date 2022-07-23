<?php

namespace MyriadDataStore\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use MyriadDataStore\Actions\DownloadMyriadContact;
use MyriadDataStore\MyriadDataDownloader;
use MyriadSoap\MyriadSoap;

class DownloadMyriadContactActionTest extends TestCase
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
                 'Contact_ID'       => 50,
                 'Forename'         => 'Peterson',
                 'ContactType_ID'   => '3',
             ]);

        $mock->shouldReceive('__soapCall')
             ->with('SOAP_getContactCommunications', \Hamcrest\Core\IsTypeOf::typeOf('array'))
             ->andReturn(
                 [
                     'ContactCommunication' => [
                         '410839;12;01279 506193;No',
                         '410840;14;bar.tr@pg.com;No',
                         '410841;14;foo.tr@pg.com;Yes',
                         'WRONG;14;quz.tr@pg.com;Yes',
                         '410842;12;07825 978 907;Yes',
                     ],
                 ]
             );

        $myriadContact = (new DownloadMyriadContact())->execute(50);

        $this->assertInstanceOf(MyriadDataDownloader::$contactModel, $myriadContact);
        $this->assertTrue($myriadContact->exists);
        $this->assertEquals('foo.tr@pg.com', $myriadContact->email);
        $this->assertEquals(3, $myriadContact->contact_type_id);
        $this->assertCount(4, $myriadContact->communications->getRawData());
        $this->assertEquals('Peterson', $myriadContact->details->getAttribute('Forename'));
    }

    /** @test */
    public function download_contact_if_communications_is_string()
    {
        /** @var \Mockery\Mock $mock */
        $mock = MyriadSoap::mockClient();

        $mock->shouldReceive('__soapCall')
             ->with('SOAP_getContactDetails', \Hamcrest\Core\IsTypeOf::typeOf('array'))
             ->andReturn([
                 'Contact_ID'       => 50,
                 'Forename'         => 'Peterson',
                 'ContactType_ID'   => '',
             ]);

        $mock->shouldReceive('__soapCall')
             ->with('SOAP_getContactCommunications', \Hamcrest\Core\IsTypeOf::typeOf('array'))
             ->andReturn('410840;14;bar.tr@pg.com;No');

        $myriadContact = (new DownloadMyriadContact())->execute(50);

        $this->assertInstanceOf(MyriadDataDownloader::$contactModel, $myriadContact);
        $this->assertTrue($myriadContact->exists);
        $this->assertEquals('bar.tr@pg.com', $myriadContact->email);
        $this->assertEquals(null, $myriadContact->contact_type_id);
        $this->assertCount(1, $myriadContact->communications->getRawData());
        $this->assertEquals('Peterson', $myriadContact->details->getAttribute('Forename'));
    }

    /** @test */
    public function error_if_wrong_id()
    {
        /** @var \Mockery\Mock $mock */
        $mock = MyriadSoap::mockClient();

        $mock->shouldReceive('__soapCall')
             ->with('SOAP_getContactDetails', \Hamcrest\Core\IsTypeOf::typeOf('array'))
             ->andReturn([
                 'Contact_ID' => 51,
                 'Forename'   => 'Peterson',
             ]);

        $this->expectExceptionMessage('Wrong details data returned from API');

        (new DownloadMyriadContact())->execute(50);
    }
}
