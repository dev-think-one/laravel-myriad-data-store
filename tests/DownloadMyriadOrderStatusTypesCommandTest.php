<?php

namespace MyriadDataStore\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use MyriadDataStore\Models\MyriadOrderStatusType;
use MyriadSoap\MyriadSoap;

class DownloadMyriadOrderStatusTypesCommandTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function download()
    {
        /** @var \Mockery\Mock $mock */
        $mock = MyriadSoap::mockClient();

        $mock->shouldReceive('__soapCall')
             ->with('SOAP_getOrderStatusTypes', \Hamcrest\Core\IsTypeOf::typeOf('array'))
             ->andReturn([
                 'OrderStatusType' => [
                     '3;Firm',
                     'WRONG;Invoiced',
                     '10;Paid',
                 ],
             ]);

        $this->artisan('myriad-download:order-status-types')
             ->assertSuccessful()
             ->expectsOutput('Successful downloaded: 2 order status types.');

        $this->assertEquals(2, MyriadOrderStatusType::count());
        $myriadOrderStatusType = MyriadOrderStatusType::find(10);
        $this->assertInstanceOf(MyriadOrderStatusType::class, $myriadOrderStatusType);
        $this->assertEquals('Paid', $myriadOrderStatusType->type);
    }
}
