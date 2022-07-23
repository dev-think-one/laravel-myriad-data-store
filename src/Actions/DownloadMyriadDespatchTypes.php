<?php

namespace MyriadDataStore\Actions;

use Illuminate\Support\Str;
use MyriadDataStore\Models\MyriadDespatchType;
use MyriadSoap\Exceptions\UnexpectedTypeException;
use MyriadSoap\MyriadSoap;

class DownloadMyriadDespatchTypes
{
    use UsesCollectionFormat;

    public static ?array $collectionFormat = null;

    protected function defaultCollectionFormat(): array
    {
        return [
            'DespatchCategory_ID' => fn ($i) => (int) tap($i, fn () => throw_if(!is_numeric($i), UnexpectedTypeException::class)),
            'DespatchType_ID'     => fn ($i) => (int) tap($i, fn () => throw_if(!is_numeric($i), UnexpectedTypeException::class)),
            'DespatchType'        => fn ($i) => (string) $i,
        ];
    }

    public function execute(): int
    {
        $formattedCollection = MyriadSoap::SOAP_getDespatchTypes_Collection([], $this->collectionFormat());

        $count = 0;
        foreach ($formattedCollection as $item) {
            $myriadModel = MyriadDespatchType::find($item['DespatchType_ID']);
            if (!$myriadModel) {
                $myriadModel     = new MyriadDespatchType;
                $myriadModel->id = $item['DespatchType_ID'];
            }
            $myriadModel->type                 = Str::limit($item['DespatchType'], 252);
            $myriadModel->despatch_category_id = $item['DespatchCategory_ID'];
            $myriadModel->save();
            $count++;
        }

        return $count;
    }
}
