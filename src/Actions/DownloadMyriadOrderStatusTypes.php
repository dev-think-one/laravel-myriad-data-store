<?php

namespace MyriadDataStore\Actions;

use Illuminate\Support\Str;
use MyriadDataStore\Models\MyriadOrderStatusType;
use MyriadSoap\Exceptions\UnexpectedTypeException;
use MyriadSoap\MyriadSoap;

class DownloadMyriadOrderStatusTypes
{
    use UsesCollectionFormat;

    public static ?array $collectionFormat = null;

    protected function defaultCollectionFormat(): array
    {
        return [
            'OrderStatusType_ID'  => fn ($i) => (int) tap($i, fn () => throw_if(!is_numeric($i), UnexpectedTypeException::class)),
            'OrderStatusType'     => fn ($i) => (string) $i,
        ];
    }

    public function execute(): int
    {
        $formattedCollection = MyriadSoap::SOAP_getOrderStatusTypes_Collection([], $this->collectionFormat());

        $count = 0;
        foreach ($formattedCollection as $item) {
            $myriadModel = MyriadOrderStatusType::find($item['OrderStatusType_ID']);
            if (!$myriadModel) {
                $myriadModel     = new MyriadOrderStatusType;
                $myriadModel->id = $item['OrderStatusType_ID'];
            }
            $myriadModel->type     = Str::limit($item['OrderStatusType'], 252);
            $myriadModel->save();
            $count++;
        }

        return $count;
    }
}
