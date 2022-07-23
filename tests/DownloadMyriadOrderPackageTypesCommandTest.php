<?php

namespace MyriadDataStore\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use MyriadDataStore\Models\MyriadOrderPackageType;
use MyriadSoap\MyriadSoap;

class DownloadMyriadOrderPackageTypesCommandTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function download()
    {
        /** @var \Mockery\Mock $mock */
        $mock = MyriadSoap::mockClient();

        $mock->shouldReceive('__soapCall')
             ->with('SOAP_getOrderPackageTypes', \Hamcrest\Core\IsTypeOf::typeOf('array'))
             ->andReturn([
                 'OrderPackageType' => [
                     [
                         'OrderPackageCategory' => 'Advertising',
                         'OrderPackageType_ID'  => '1',
                         'OrderPackageType'     => 'Advertising',
                     ],
                     [
                         'OrderPackageType_ID' => '9',
                         'OrderPackageType'    => 'Awards',
                     ],
                     [
                         'OrderPackageCategory' => 'Book Cat',
                         'OrderPackageType_ID'  => '6',
                         'OrderPackageType'     => 'Book',
                     ],

                 ],
             ]);

        $this->artisan('myriad-download:order-package-types')
             ->assertSuccessful()
             ->expectsOutput('Successful downloaded: 2 order package types.');

        $this->assertEquals(2, MyriadOrderPackageType::count());
        $myriadProductType = MyriadOrderPackageType::find(6);
        $this->assertInstanceOf(MyriadOrderPackageType::class, $myriadProductType);
        $this->assertEquals('Book', $myriadProductType->type);
        $this->assertEquals('Book Cat', $myriadProductType->category);
    }
}
