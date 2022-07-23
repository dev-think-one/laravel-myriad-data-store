<?php

namespace MyriadDataStore\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use MyriadDataStore\Models\MyriadProductType;
use MyriadSoap\MyriadSoap;

class DownloadMyriadProductTypesCommandTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function download()
    {
        /** @var \Mockery\Mock $mock */
        $mock = MyriadSoap::mockClient();

        $mock->shouldReceive('__soapCall')
             ->with('SOAP_getProductTypes', \Hamcrest\Core\IsTypeOf::typeOf('array'))
             ->andReturn([
                 'ProductType' => [
                     '5;Magazine;Magazine',
                     'WRONG;Book from Stock;Book - From Stock',
                     '7;Book from Stock;Book - From Stock',
                     '77WrongSepCOUNT from Stock;Book - From Stock',
                 ],
             ]);

        $this->artisan('myriad-download:product-types')
             ->assertSuccessful()
             ->expectsOutput('Successful downloaded: 2 product types.');

        $this->assertEquals(2, MyriadProductType::count());
        $myriadProductType = MyriadProductType::find(7);
        $this->assertInstanceOf(MyriadProductType::class, $myriadProductType);
        $this->assertEquals('Book from Stock', $myriadProductType->type);
        $this->assertEquals('Book - From Stock', $myriadProductType->category);
    }
}
