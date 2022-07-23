<?php

namespace MyriadDataStore\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use MyriadDataStore\Models\MyriadContactType;
use MyriadSoap\MyriadSoap;

class DownloadMyriadContactTypesCommandTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function download()
    {
        /** @var \Mockery\Mock $mock */
        $mock = MyriadSoap::mockClient();

        $mock->shouldReceive('__soapCall')
             ->with('SOAP_getContactTypes', \Hamcrest\Core\IsTypeOf::typeOf('array'))
             ->andReturn([
                 'ContactType' => [
                     [
                         'ContactType_ID'            => '6',
                         'ContactType'               => 'Customer',
                         'AreContactTitlesMandatory' => 'false',
                     ],
                     [
                         'ContactType_ID'            => '12',
                         'ContactType'               => 'Company',
                         'AreContactTitlesMandatory' => 'true',
                     ],
                 ],
             ]);

        $this->artisan('myriad-download:contact-types')
             ->assertSuccessful()
             ->expectsOutput('Successful downloaded: 2 contact types.');

        $this->assertEquals(2, MyriadContactType::count());

        $myriadContactType = MyriadContactType::find(6);
        $this->assertInstanceOf(MyriadContactType::class, $myriadContactType);
        $this->assertEquals('Customer', $myriadContactType->type);
        $this->assertFalse($myriadContactType->requires_titles);

        $myriadContactType = MyriadContactType::find(12);
        $this->assertInstanceOf(MyriadContactType::class, $myriadContactType);
        $this->assertEquals('Company', $myriadContactType->type);
        $this->assertTrue($myriadContactType->requires_titles);
    }
}
