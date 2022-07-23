<?php

namespace MyriadDataStore\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use MyriadDataStore\Models\MyriadOrder;
use MyriadDataStore\Models\MyriadOrderPackage;
use MyriadSoap\MyriadSoap;

class DownloadMyriadContactOrdersBasicCommandTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function download_contact_orders()
    {
        /** @var \Mockery\Mock $mock */
        $mock = MyriadSoap::mockClient();

        $mock->shouldReceive('__soapCall')
             ->with('SOAP_getOrdersBasic', \Hamcrest\Core\IsTypeOf::typeOf('array'))
             ->andReturn(
                 [
                     'Order' => [
                         [
                             'OrderNumber'         => 123,
                             'SubsPromotion'       => 'UK',
                             'Currency'            => 'UK Pounds',
                             'PaymentType'         => 'Cash.Cheque',
                             'StatusType'          => 'Cancelled',
                             'Amount'              => '',
                             'CollectionFrequency' => 'Annual',
                             'Invoice_Contact_ID'  => '130',
                             'Invoice_Contact'     => 'Mr Smith',
                             'Despatch_Contact_ID' => '131',
                             'Despatch_Contact'    => 'Mr Smith 1',
                             'Agent_Contact_ID'    => null,
                             'Agent_Contact'       => null,
                             'CreationDate'        => '2019-03-16',
                             'Packages'            => [
                                 'Package' => [
                                     [
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
                                     [
                                         'OrderPackageType_ID' => 2,
                                         'Title_ID'            => 14,
                                         'StartIssue'          => 'Annual',
                                         'EndIssue'            => null,
                                         'Amount'              => '00.00',
                                         'StatusType'          => 'Cancelled',
                                         'StopCode'            => 'Live',
                                         'OrderPackageType'    => 'Subscription',
                                         'Title'               => 'Main Pub',
                                         'MyriadPackage_ID'    => 321,
                                         'RemainingIssues'     => 1344,
                                         'Copies'              => 2,
                                     ],
                                 ],
                             ],
                         ],
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
                         ],
                     ],
                 ]
             );

        Artisan::call('myriad-download:contact-orders-basic 22');

        $myriadOrder = MyriadOrder::find(123);
        $this->assertInstanceOf(MyriadOrder::class, $myriadOrder);
        $this->assertEquals('Cancelled', $myriadOrder->status);
        $this->assertEquals(130, $myriadOrder->invoice_contact_id);
        $this->assertEquals(131, $myriadOrder->despatch_contact_id);
        $this->assertEquals(0, $myriadOrder->agent_contact_id);
        $this->assertEquals('2019-03-16', $myriadOrder->order_date->format('Y-m-d'));
        $this->assertEquals('UK', $myriadOrder->details->getAttribute('SubsPromotion'));
        $this->assertEquals('UK Pounds', $myriadOrder->details->getAttribute('Currency'));
        $this->assertEquals('Cash.Cheque', $myriadOrder->details->getAttribute('PaymentType'));
        $this->assertEquals('', $myriadOrder->details->getAttribute('Amount'));
        $this->assertEquals('Annual', $myriadOrder->details->getAttribute('CollectionFrequency'));
        $this->assertEquals('Mr Smith', $myriadOrder->details->getAttribute('Invoice_Contact'));
        $this->assertEquals('Mr Smith 1', $myriadOrder->details->getAttribute('Despatch_Contact'));
        $this->assertEquals(null, $myriadOrder->details->getAttribute('Agent_Contact'));
        $this->assertCount(2, $myriadOrder->packages);
        $myriadPackage = $myriadOrder->packages->get(0);
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
        $myriadPackage = $myriadOrder->packages->get(1);
        $this->assertInstanceOf(MyriadOrderPackage::class, $myriadPackage);
        $this->assertEquals(2, $myriadPackage->order_package_type_id);
        $this->assertEquals(14, $myriadPackage->title_id);
        $this->assertEquals('Annual', $myriadPackage->start_issue);
        $this->assertEquals(null, $myriadPackage->end_issue);
        $this->assertEquals('Cancelled', $myriadPackage->status);
        $this->assertEquals('Live', $myriadPackage->stopcode);
        $this->assertEquals(321, $myriadPackage->myriad_package_id);
        $this->assertEquals(1344, $myriadPackage->remaining_issues);
        $this->assertEquals(2, $myriadPackage->copies);
        $this->assertEquals('00.00', $myriadPackage->details->getAttribute('Amount'));
        $this->assertEquals('Subscription', $myriadPackage->details->getAttribute('OrderPackageType'));
        $this->assertEquals('Main Pub', $myriadPackage->details->getAttribute('Title'));

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
}
