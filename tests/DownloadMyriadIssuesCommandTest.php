<?php

namespace MyriadDataStore\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use MyriadDataStore\Models\MyriadIssue;
use MyriadSoap\MyriadSoap;

class DownloadMyriadIssuesCommandTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function download()
    {
        /** @var \Mockery\Mock $mock */
        $mock = MyriadSoap::mockClient();

        $mock->shouldReceive('__soapCall')
             ->with('SOAP_getIssues', \Hamcrest\Core\IsTypeOf::typeOf('array'))
             ->andReturn([
                 'Issue' => [
                     '103;2;2007 - Issue 01;82;2007-01-01',
                     '104;Wrong;2007 - Issue 02;83;2007-03-01',
                     '466;6;2007 - Issue 01;117;2007-01-01',
                     'Wrong;6;2007 - Issue 03;119;2007-05-01',
                 ],
             ]);

        $this->artisan('myriad-download:issues')
             ->assertSuccessful()
             ->expectsOutput('Successful downloaded: 2 issues.');

        $this->assertEquals(2, MyriadIssue::count());
        $model = MyriadIssue::find(466);
        $this->assertInstanceOf(MyriadIssue::class, $model);
        $this->assertEquals('2007 - Issue 01', $model->name);
        $this->assertEquals(6, $model->title_id);
        $this->assertEquals(117, $model->number);
        $this->assertEquals('2007_01_01', $model->publication_date->format('Y_m_d'));
    }

    /** @test */
    public function download_for_title()
    {
        /** @var \Mockery\Mock $mock */
        $mock = MyriadSoap::mockClient();

        $mock->shouldReceive('__soapCall')
             ->with('SOAP_getIssuesForTitle', \Hamcrest\Core\IsTypeOf::typeOf('array'))
             ->andReturn([
                 'Issue' => [
                     '103;2;2007 - Issue 01;82;2007-01-01',
                     '104;Wrong;2007 - Issue 02;83;2007-03-01',
                     '466;6;2007 - Issue 01;117;2007-01-01',
                     'Wrong;6;2007 - Issue 03;119;2007-05-01',
                 ],
             ]);

        $this->artisan('myriad-download:issues 23')
             ->assertSuccessful()
             ->expectsOutput('Successful downloaded: 2 issues.');

        $this->assertEquals(2, MyriadIssue::count());
        $model = MyriadIssue::find(466);
        $this->assertInstanceOf(MyriadIssue::class, $model);
        $this->assertEquals('2007 - Issue 01', $model->name);
        $this->assertEquals(6, $model->title_id);
        $this->assertEquals(117, $model->number);
        $this->assertEquals('2007_01_01', $model->publication_date->format('Y_m_d'));
    }
}
