<?php

namespace MyriadDataStore\Actions;

use Illuminate\Support\Str;
use MyriadDataStore\Models\MyriadProductType;
use MyriadSoap\Exceptions\UnexpectedTypeException;
use MyriadSoap\MyriadSoap;

class DownloadMyriadProductTypes
{
    use UsesCollectionFormat;

    public static ?array $collectionFormat = null;

    protected function defaultCollectionFormat(): array
    {
        return [
            'ProductType_ID'  => fn ($i) => (int) tap($i, fn () => throw_if(!is_numeric($i), UnexpectedTypeException::class)),
            'ProductType'     => fn ($i) => (string) $i,
            'ProductCategory' => fn ($i) => (string) $i,
        ];
    }

    public function execute(): int
    {
        $formattedCollection = MyriadSoap::SOAP_getProductTypes_Collection([], $this->collectionFormat());

        $count = 0;
        foreach ($formattedCollection as $item) {
            $myriadModel = MyriadProductType::find($item['ProductType_ID']);
            if (!$myriadModel) {
                $myriadModel     = new MyriadProductType;
                $myriadModel->id = $item['ProductType_ID'];
            }
            $myriadModel->type     =  Str::limit($item['ProductType'], 252);
            $myriadModel->category = Str::limit($item['ProductCategory'], 252);
            $myriadModel->save();
            $count++;
        }

        return $count;
    }
}
