<?php

namespace MyriadDataStore\Actions;

use Illuminate\Support\Str;
use MyriadDataStore\Models\MyriadTitle;
use MyriadSoap\Exceptions\UnexpectedTypeException;
use MyriadSoap\MyriadSoap;

class DownloadMyriadTitles
{
    use UsesCollectionFormat;

    public static ?array $collectionFormat = null;

    protected function defaultCollectionFormat(): array
    {
        return [
            'Title_ID'         => fn ($i) => (int) tap($i, fn () => throw_if(!is_numeric($i), UnexpectedTypeException::class)),
            'Title'            => fn ($i) => (string) $i,
            'ProductType_ID'   => fn ($i) => (int) tap($i, fn () => throw_if(!is_numeric($i), UnexpectedTypeException::class)),
            'Current_Issue_ID' => fn ($i) => (int) tap($i, fn () => throw_if(!is_numeric($i), UnexpectedTypeException::class)),
            'Active'           => fn ($i) => $i == 'Yes',
        ];
    }

    public function execute(): int
    {
        $formattedCollection = MyriadSoap::SOAP_getTitles_Collection([], $this->collectionFormat());

        $count = 0;
        foreach ($formattedCollection as $item) {
            $myriadModel = MyriadTitle::find($item['Title_ID']);
            if (!$myriadModel) {
                $myriadModel     = new MyriadTitle;
                $myriadModel->id = $item['Title_ID'];
            }
            $myriadModel->title            = Str::limit($item['Title'], 252);
            $myriadModel->product_type_id  = $item['ProductType_ID'];
            $myriadModel->current_issue_id = $item['Current_Issue_ID'];
            $myriadModel->active           = $item['Active'];
            $myriadModel->save();
            $count++;
        }

        return $count;
    }
}
