<?php

namespace MyriadDataStore\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use MyriadDataStore\Jobs\DownloadMyriadOrdersBasicForContacts;
use MyriadDataStore\Models\MyriadOrder;
use MyriadDataStore\Models\MyriadOrderPackage;
use MyriadSoap\MyriadSoap;

class DownloadMyriadOrdersBasicForContactsCommandTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function download_contacts()
    {
        /** @var \Mockery\Mock $mock */
        $mock = MyriadSoap::mockClient();

        $mock->shouldReceive('__soapCall')
             ->with('SOAP_getOrdersBasic', \Hamcrest\Core\IsTypeOf::typeOf('array'))
             ->andReturn(
                 [
                     'OrderNumber'         => 456,
                     'SubsPromotion'       => 'UK',
                     'Currency'            => 'UK Pounds',
                     'PaymentType'         => 'Cash.Cheque',
                     'StatusType'          => 'Cancelled',
                     'Amount'              => '',
                     'CollectionFrequency' => 'Annual',
                     'Invoice_Contact_ID'  => '130',
                     'Invoice_Contact'     => 'Mr Smith',
                     'Despatch_Contact_ID' => '130',
                     'Despatch_Contact'    => 'Mr Smith',
                     'Agent_Contact_ID'    => 120,
                     'Agent_Contact'       => 'Mr Smith 120',
                     'CreationDate'        => '2020-03-16',
                     'Packages'            => [
                         'Package' => [
                             'OrderPackageType_ID' => 5,
                             'Title_ID'            => 1,
                             'StartIssue'          => 'Jan 2010',
                             'EndIssue'            => 'Dec 2010',
                             'Amount'              => '10.45',
                             'StatusType'          => 'Cancelled',
                             'StopCode'            => 'Live',
                             'OrderPackageType'    => 'Book',
                             'Title'               => 'Offer Book',
                             'MyriadPackage_ID'    => null,
                             'RemainingIssues'     => 1344,
                             'Copies'              => 1,
                         ],
                     ],
                 ]
             );

        $this->assertNull(MyriadOrder::find(456));

        Artisan::call('myriad-download:contacts-orders-basic 564 --count=1');

        $myriadOrder = MyriadOrder::find(456);
        $this->assertInstanceOf(MyriadOrder::class, $myriadOrder);
        $this->assertEquals('Cancelled', $myriadOrder->status);
        $this->assertEquals(130, $myriadOrder->invoice_contact_id);
        $this->assertEquals(130, $myriadOrder->despatch_contact_id);
        $this->assertEquals(120, $myriadOrder->agent_contact_id);
        $this->assertEquals('2020-03-16', $myriadOrder->order_date->format('Y-m-d'));
        $this->assertEquals('UK', $myriadOrder->details->getAttribute('SubsPromotion'));
        $this->assertEquals('UK Pounds', $myriadOrder->details->getAttribute('Currency'));
        $this->assertEquals('Cash.Cheque', $myriadOrder->details->getAttribute('PaymentType'));
        $this->assertEquals('', $myriadOrder->details->getAttribute('Amount'));
        $this->assertEquals('Annual', $myriadOrder->details->getAttribute('CollectionFrequency'));
        $this->assertEquals('Mr Smith', $myriadOrder->details->getAttribute('Invoice_Contact'));
        $this->assertEquals('Mr Smith', $myriadOrder->details->getAttribute('Despatch_Contact'));
        $this->assertEquals('Mr Smith 120', $myriadOrder->details->getAttribute('Agent_Contact'));
        $this->assertCount(1, $myriadOrder->packages);
        $myriadPackage = $myriadOrder->packages->first();
        $this->assertInstanceOf(MyriadOrderPackage::class, $myriadPackage);
        $this->assertEquals(5, $myriadPackage->order_package_type_id);
        $this->assertEquals(1, $myriadPackage->title_id);
        $this->assertEquals('Jan 2010', $myriadPackage->start_issue);
        $this->assertEquals('Dec 2010', $myriadPackage->end_issue);
        $this->assertEquals('Cancelled', $myriadPackage->status);
        $this->assertEquals('Live', $myriadPackage->stopcode);
        $this->assertEquals(null, $myriadPackage->myriad_package_id);
        $this->assertEquals(1344, $myriadPackage->remaining_issues);
        $this->assertEquals(1, $myriadPackage->copies);
        $this->assertEquals('10.45', $myriadPackage->details->getAttribute('Amount'));
        $this->assertEquals('Book', $myriadPackage->details->getAttribute('OrderPackageType'));
        $this->assertEquals('Offer Book', $myriadPackage->details->getAttribute('Title'));
    }

    /** @test */
    public function download_in_queue_contacts()
    {
        Queue::fake();

        Queue::assertNothingPushed();

        $this->artisan('myriad-download:contacts-orders-basic 564 --count=1 --queue=foo')
             ->assertSuccessful()
             ->expectsOutput('Job sent to queue [foo].');

        Queue::assertPushedOn('foo', DownloadMyriadOrdersBasicForContacts::class);
    }

    /** @test */
    public function error_response()
    {
        /** @var \Mockery\Mock $mock */
        $mock = MyriadSoap::mockClient();

        $mock->shouldReceive('__soapCall')
             ->with('SOAP_getOrdersBasic', \Hamcrest\Core\IsTypeOf::typeOf('array'))
             ->andReturn(['faultcode' => 'faultcodeTest', 'faultstring' => 'faultstringTest']);
        ;

        $this->artisan('myriad-download:contacts-orders-basic 564 --count=1')
             ->assertSuccessful()
             ->expectsOutput('Successful downloaded orders for 0 contacts.')
             ->expectsOutput('Fails downloaded orders for 1 contacts.');
    }

    /** @test */
    public function error_arguments()
    {
        $this->artisan('myriad-download:contacts-orders-basic 0 --count=1')
             ->assertExitCode(1)
             ->expectsOutput('Invalid start value [0]');

        $this->artisan('myriad-download:contacts-orders-basic --count=100001')
             ->assertExitCode(1)
             ->expectsOutput('Invalid count value [100001]');

        $this->artisan('myriad-download:contacts-orders-basic --count=0')
             ->assertExitCode(1)
             ->expectsOutput('Invalid count value [0]');
    }
}
