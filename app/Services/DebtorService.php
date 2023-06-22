<?php

namespace App\Services;

use App\Models\Debtor;

class DebtorService
{
    private $csvService;

    public function __construct(CsvService $csvService)
    {
        $this->csvService = $csvService;
    }

    public function registerDebtors()
    {
        $debtors = $this->csvService->retrieveCSVData('storage/app'.env('SFTP_LOCAL_PATH', '/csv').'/debiteuren.csv');

        // TODO check if a debtor is in the database that should not be there
        collect($debtors)->each(function ($debtor) {
            if ($existingDebtor = $this->debtorExists($debtor['Debiteurnummer'])) {
                $this->updateDebtor($existingDebtor, $debtor);
            } else {
                $this->registerNewDebtor($debtor);
            }
        });
    }

    private function updateDebtor(Debtor $existingDebtor, array $debtor): void
    {
        $existingDebtor->update($this->mapDebtorData($debtor));
    }

    private function registerNewDebtor(array $debtor): void
    {
        $debtor = Debtor::create(
            array_merge([
                'debtor_number' => $debtor['Debiteurnummer'],
                $this->mapDebtorData($debtor),
            ])
        );
    }

    private function debtorExists(string $debtorNumber): ?Debtor
    {
        return Debtor::where('debtor_number', $debtorNumber)->first();
    }

    private function mapDebtorData(array $debtor): array
    {
        return [
            'name_1' => $debtor['Naam1'],
            'name_2' => $debtor['Naam2'],
            'search_name' => $debtor['Zoeknaam'],
            'address' => $debtor['Adres'],
            'postalcode' => $debtor['Postcode'],
            'city' => $debtor['Plaats'],
            'country' => $debtor['Land'],
            'contact' => $debtor['Contact'],
            'phonenumber' => $debtor['Telefoon'],
            'mobile' => $debtor['Mobiel'],
            'email' => $debtor['Email'],
            'email_cc' => $debtor['Email cc'],
            'email_invoice' => $debtor['Email factuur'],
            'email_invoice_cc' => $debtor['Email factuur cc'],
            'tax_number' => $debtor['BTW nummer'],
        ];
    }
}
