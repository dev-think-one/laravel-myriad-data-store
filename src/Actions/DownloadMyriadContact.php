<?php

namespace MyriadDataStore\Actions;

use MyriadDataStore\MyriadDataDownloader;
use MyriadSoap\Exceptions\UnexpectedTypeException;
use MyriadSoap\MyriadSoap;

class DownloadMyriadContact
{
    use UsesCollectionFormat;

    public static ?array $collectionFormat = null;

    protected function defaultCollectionFormat(): array
    {
        return [
            'ContactCommunication_ID' => fn ($i) => (int) tap($i, fn () => throw_if(!is_numeric($i), UnexpectedTypeException::class)),
            'DespatchType_ID'         => fn ($i) => (int) tap($i, fn () => throw_if(!is_numeric($i), UnexpectedTypeException::class)),
            'ContactCommunication'    => fn ($i) => (string) $i,
            'PrimaryUse'              => fn ($i) => $i == 'Yes',
        ];
    }

    public function execute(int $myriadContactID)
    {
        $details = MyriadSoap::SOAP_getContactDetails(['Contact_ID' => $myriadContactID]);
        if (!is_array($details)
            || empty($details['Contact_ID'])
            || $details['Contact_ID'] != $myriadContactID) {
            throw new \Exception('Wrong details data returned from API ['.$myriadContactID.'|'.($details['Contact_ID'] ?? '-').']');
        }

        $formattedCommunications = MyriadSoap::SOAP_getContactCommunications_Collection(
            ['Contact_ID' => $myriadContactID],
            $this->collectionFormat()
        );

        $primaryEmail = ($formattedCommunications->filter(fn ($formattedCommunication) => filter_var($formattedCommunication['ContactCommunication'], FILTER_VALIDATE_EMAIL))
                                                 ->sortBy(function ($formattedCommunication) {
                                                     return $formattedCommunication['PrimaryUse'] ? -1 : 1;
                                                 })
                                                 ->first() ?? [])['ContactCommunication'] ?? null;

        $modelClass         = MyriadDataDownloader::$contactModel;
        $myriadContactModel = $modelClass::find($myriadContactID);
        if (!$myriadContactModel) {
            $myriadContactModel     = new $modelClass;
            $myriadContactModel->id = $myriadContactID;
        }

        $myriadContactModel->contact_type_id = (!empty($details['ContactType_ID']) && is_numeric($details['ContactType_ID'])) ? ((int) $details['ContactType_ID']) : null;
        $myriadContactModel->email           = $primaryEmail;
        $myriadContactModel->details->setData($details);
        $myriadContactModel->communications->setData($formattedCommunications->toArray());
        $myriadContactModel->save();

        return $myriadContactModel;
    }
}
