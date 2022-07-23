<?php

namespace MyriadDataStore\Actions;

use Carbon\Carbon;
use Illuminate\Support\Str;
use MyriadDataStore\Models\MyriadOrder;
use MyriadSoap\Exceptions\UnexpectedTypeException;
use MyriadSoap\MyriadSoap;

class DownloadMyriadContactOrdersBasic
{
    use UsesCollectionFormat;

    public static ?array $collectionFormat = null;

    protected function defaultCollectionFormat(): array
    {
        return [
            'OrderNumber'         => fn ($i) => (int) tap($i, fn () => throw_if(!is_numeric($i), UnexpectedTypeException::class)),
            'SubsPromotion'       => fn ($i) => (string) $i,
            'Currency'            => fn ($i) => (string) $i,
            'PaymentType'         => fn ($i) => (string) $i,
            'StatusType'          => fn ($i) => (string) $i,
            'Amount'              => fn ($i) => (string) $i,
            'CollectionFrequency' => fn ($i) => (string) $i,
            'Invoice_Contact_ID'  => fn ($i) => (int) $i,
            'Invoice_Contact'     => fn ($i) => (string) $i,
            'Despatch_Contact_ID' => fn ($i) => (int) $i,
            'Despatch_Contact'    => fn ($i) => (string) $i,
            'Agent_Contact_ID'    => fn ($i) => (int) $i,
            'Agent_Contact'       => fn ($i) => (string) $i,
            'CreationDate'        => fn ($i) => Carbon::createFromFormat('Y-m-d', $i),
            'Packages'            => function ($i) {
                if (!isset($i['Package'])
                    || !is_array($i['Package'])) {
                    // Debug!; TODO: delete it
                    throw new \Exception('Package key not exists in array!!!! DEBUG');

                    return [];
                }

                if (isset($i['Package']['OrderPackageType_ID'])) {
                    return [$i['Package']];
                }

                return $i['Package'];
            },
        ];
    }

    public function execute(int $myriadContactID)
    {
        $formattedCollection = MyriadSoap::SOAP_getOrdersBasic_AssocCollection(
            ['Contact_ID' => $myriadContactID],
            $this->collectionFormat(),
            'Order'
        );

        $count = 0;

        foreach ($formattedCollection as $item) {
            $myriadModel = MyriadOrder::find($item['OrderNumber']);
            if (!$myriadModel) {
                $myriadModel     = new MyriadOrder;
                $myriadModel->id = $item['OrderNumber'];
            }
            $myriadModel->status              = Str::limit($item['StatusType'], 252);
            $myriadModel->invoice_contact_id  = $item['Invoice_Contact_ID'];
            $myriadModel->despatch_contact_id = $item['Despatch_Contact_ID'];
            $myriadModel->agent_contact_id    = $item['Agent_Contact_ID'];
            $myriadModel->order_date          = $item['CreationDate'];
            $myriadModel->details->setData([
                'SubsPromotion'       => $item['SubsPromotion'],
                'Currency'            => $item['Currency'],
                'PaymentType'         => $item['PaymentType'],
                'Amount'              => $item['Amount'],
                'CollectionFrequency' => $item['CollectionFrequency'],
                'Invoice_Contact'     => $item['Invoice_Contact'],
                'Despatch_Contact'    => $item['Despatch_Contact'],
                'Agent_Contact'       => $item['Agent_Contact'],
            ]);
            $myriadModel->save();

            $myriadModel->packages()->delete();

            foreach ($item['Packages'] as $package) {
                $myriadModel->packages()->create([
                    'order_package_type_id' => (int) ($package['OrderPackageType_ID'] ?? 0),
                    'title_id'              => (int) ($package['Title_ID'] ?? 0),
                    'start_issue'           => (string) ($package['StartIssue'] ?? ''),
                    'end_issue'             => (string) ($package['EndIssue'] ?? ''),
                    'status'                => (string) ($package['StatusType'] ?? ''),
                    'stopcode'              => (string) ($package['StopCode'] ?? ''),
                    'myriad_package_id'     => !empty($package['MyriadPackage_ID']) ? ((int) $package['MyriadPackage_ID']) : null,
                    'remaining_issues'      => (int) ($package['RemainingIssues'] ?? 0),
                    'copies'                => (int) ($package['Copies'] ?? 0),
                    'details'               => [
                        'Amount'           => (string) ($package['Amount'] ?? ''),
                        'OrderPackageType' => (string) ($package['OrderPackageType'] ?? ''),
                        'Title'            => (string) ($package['Title'] ?? ''),
                    ],
                ]);
            }

            $count++;
        }

        return $count;
    }
}
