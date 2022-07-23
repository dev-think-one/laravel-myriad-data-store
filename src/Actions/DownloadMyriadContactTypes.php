<?php

namespace MyriadDataStore\Actions;

use Illuminate\Support\Str;
use MyriadDataStore\Models\MyriadContactType;
use MyriadSoap\Exceptions\UnexpectedTypeException;
use MyriadSoap\MyriadSoap;

class DownloadMyriadContactTypes
{
    use UsesCollectionFormat;

    public static ?array $collectionFormat = null;

    protected function defaultCollectionFormat(): array
    {
        return [
            'ContactType_ID'            => fn ($i) => (int) tap($i, fn () => throw_if(!is_numeric($i), UnexpectedTypeException::class)),
            'ContactType'               => fn ($i) => (string) $i,
            'AreContactTitlesMandatory' => fn ($i) => $i == 'true',
        ];
    }

    public function execute(): int
    {
        $formattedCollection = MyriadSoap::SOAP_getContactTypes_AssocCollection([], $this->collectionFormat());

        $count = 0;
        foreach ($formattedCollection as $item) {
            $myriadModel = MyriadContactType::find($item['ContactType_ID']);
            if (!$myriadModel) {
                $myriadModel     = new MyriadContactType;
                $myriadModel->id = $item['ContactType_ID'];
            }
            $myriadModel->type            = Str::limit($item['ContactType'], 252);
            $myriadModel->requires_titles = $item['AreContactTitlesMandatory'];
            $myriadModel->save();
            $count++;
        }

        return $count;
    }
}
