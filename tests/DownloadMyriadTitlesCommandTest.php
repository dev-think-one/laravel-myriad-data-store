<?php

namespace MyriadDataStore\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use MyriadDataStore\Models\MyriadTitle;
use MyriadSoap\MyriadSoap;

class DownloadMyriadTitlesCommandTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function download()
    {
        /** @var \Mockery\Mock $mock */
        $mock = MyriadSoap::mockClient();

        $mock->shouldReceive('__soapCall')
             ->with('SOAP_getTitles', \Hamcrest\Core\IsTypeOf::typeOf('array'))
             ->andReturn([
                 'Title' => [
                     '2;Title1;5;107;Yes',
                     '4;Title1;Wrong;107;Yes',
                     '6;Title 2;9;497;No',
                     'Wrong;Title 2;9;497;No',
                 ],
             ]);

        $this->artisan('myriad-download:titles')
             ->assertSuccessful()
             ->expectsOutput('Successful downloaded: 2 titles.');

        $this->assertEquals(2, MyriadTitle::count());
        $model = MyriadTitle::find(2);
        $this->assertInstanceOf(MyriadTitle::class, $model);
        $this->assertEquals('Title1', $model->title);
        $this->assertEquals(5, $model->product_type_id);
        $this->assertEquals(107, $model->current_issue_id);
        $this->assertTrue($model->active);
    }
}
