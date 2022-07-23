<?php

namespace MyriadDataStore\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use MyriadDataStore\MyriadDataDownloader;
use MyriadSoap\MyriadSoap;

class DownloadLatestMyriadContactsCommandTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function download_contacts()
    {
        $class = MyriadDataDownloader::$contactModel;
        $class::create(['id' => 50]);

        /** @var \Mockery\Mock $mock */
        $mock = MyriadSoap::mockClient();

        for ($i = 51; $i <= 55; $i++) {
            $mock->shouldReceive('__soapCall')
                 ->with('SOAP_getContactDetails', \Hamcrest\Core\IsTypeOf::typeOf('array'))
                 ->andReturn(['Contact_ID' => $i, 'Forename' => 'Peterson',])->once();

            $mock->shouldReceive('__soapCall')
                 ->with('SOAP_getContactCommunications', \Hamcrest\Core\IsTypeOf::typeOf('array'))
                 ->andReturn(null)->once();

            $mock->shouldReceive('__soapCall')
                 ->with('SOAP_getOrdersBasic', \Hamcrest\Core\IsTypeOf::typeOf('array'))
                 ->andReturn(
                     [
                         'OrderNumber'         => $i,
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
                 )->once();
        }

        for ($i = 55; $i <= 65; $i++) {
            $mock->shouldReceive('__soapCall')
                 ->with('SOAP_getContactDetails', \Hamcrest\Core\IsTypeOf::typeOf('array'))
                 ->andReturn(['faultcode' => 'faultcodeTest', 'faultstring' => 'faultstringTest']);
        }


        Artisan::call('myriad-download:contacts:latest');

        /* $myriadContact = $class::find(564);
         $this->assertInstanceOf($class, $myriadContact);
         $this->assertEquals('foo.tr@pg.com', $myriadContact->email);
         $this->assertCount(4, $myriadContact->communications->getRawData());
         $this->assertEquals('Peterson', $myriadContact->details->getAttribute('Forename'));*/
    }
}
