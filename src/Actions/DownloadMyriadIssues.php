<?php

namespace MyriadDataStore\Actions;

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use MyriadDataStore\Models\MyriadIssue;
use MyriadSoap\Exceptions\UnexpectedTypeException;
use MyriadSoap\MyriadSoap;

class DownloadMyriadIssues
{
    use UsesCollectionFormat;

    public static ?array $collectionFormat = null;

    protected function defaultCollectionFormat(): array
    {
        return [
            'Issue_ID'        => fn ($i) => (int) tap($i, fn () => throw_if(!is_numeric($i), UnexpectedTypeException::class)),
            'Title_ID'        => fn ($i) => (int) tap($i, fn () => throw_if(!is_numeric($i), UnexpectedTypeException::class)),
            'Issue'           => fn ($i) => (string) $i,
            'IssueNumber'     => fn ($i) => (int) tap($i, fn () => throw_if(!is_numeric($i), UnexpectedTypeException::class)),
            'PublicationDate' => fn ($i) => Carbon::createFromFormat('Y-m-d', $i),
        ];
    }

    public function execute(?int $titleId = null): int
    {
        if ($titleId) {
            $formattedCollection = MyriadSoap::SOAP_getIssuesForTitle_Collection(['Title_ID' => $titleId], $this->collectionFormat(), 'Issue');
        } else {
            $formattedCollection = MyriadSoap::SOAP_getIssues_Collection([], $this->collectionFormat());
        }

        $count = 0;
        foreach ($formattedCollection as $item) {
            $myriadModel = MyriadIssue::find($item['Issue_ID']);
            if (!$myriadModel) {
                $myriadModel     = new MyriadIssue;
                $myriadModel->id = $item['Issue_ID'];
            }
            $myriadModel->name             = Str::limit($item['Issue'], 252);
            $myriadModel->title_id         = $item['Title_ID'];
            $myriadModel->number           = $item['IssueNumber'];
            $myriadModel->publication_date = $item['PublicationDate'];
            $myriadModel->save();
            $count++;
        }

        return $count;
    }
}
