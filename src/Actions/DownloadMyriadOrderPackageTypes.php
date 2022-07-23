<?php

namespace MyriadDataStore\Actions;

use Illuminate\Support\Str;
use MyriadDataStore\Models\MyriadOrderPackageType;
use MyriadSoap\Exceptions\UnexpectedTypeException;
use MyriadSoap\MyriadSoap;

class DownloadMyriadOrderPackageTypes
{
    use UsesCollectionFormat;

    public static ?array $collectionFormat = null;

    protected function defaultCollectionFormat(): array
    {
        return [
            'OrderPackageType_ID'  => fn ($i) => (int) tap($i, fn () => throw_if(!is_numeric($i), UnexpectedTypeException::class)),
            'OrderPackageType'     => fn ($i) => (string) $i,
            'OrderPackageCategory' => fn ($i) => (string) $i,
        ];
    }

    public function execute(): int
    {
        $formattedCollection = MyriadSoap::SOAP_getOrderPackageTypes_AssocCollection([], $this->collectionFormat());

        $count = 0;
        foreach ($formattedCollection as $item) {
            $myriadModel = MyriadOrderPackageType::find($item['OrderPackageType_ID']);
            if (!$myriadModel) {
                $myriadModel     = new MyriadOrderPackageType;
                $myriadModel->id = $item['OrderPackageType_ID'];
            }
            $myriadModel->type     = Str::limit($item['OrderPackageType'], 252);
            $myriadModel->category = Str::limit($item['OrderPackageCategory'], 252);
            $myriadModel->save();
            $count++;
        }

        return $count;
    }
}
