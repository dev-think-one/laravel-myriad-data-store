<?php

namespace MyriadDataStore\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use MyriadDataStore\Models\MyriadDespatchType;
use MyriadSoap\MyriadSoap;

class DownloadMyriadDespatchTypesCommandTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function download()
    {
        /** @var \Mockery\Mock $mock */
        $mock = MyriadSoap::mockClient();

        $mock->shouldReceive('__soapCall')
             ->with('SOAP_getDespatchTypes', \Hamcrest\Core\IsTypeOf::typeOf('array'))
             ->andReturn([
                 'DespatchType' => [
                     '3;1;Email',
                     'Wrong;1;Post',
                     '1;wrong;Phone',
                     '2;9;Fax',
                 ],
             ]);

        $this->artisan('myriad-download:despatch-types')
             ->assertSuccessful()
             ->expectsOutput('Successful downloaded: 2 despatch types.');

        $this->assertEquals(2, MyriadDespatchType::count());
        $myriadDespatchType = MyriadDespatchType::find(9);
        $this->assertInstanceOf(MyriadDespatchType::class, $myriadDespatchType);
        $this->assertEquals('Fax', $myriadDespatchType->type);
        $this->assertEquals(2, $myriadDespatchType->despatch_category_id);
    }
}
