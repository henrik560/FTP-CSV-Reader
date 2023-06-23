<?php

namespace App\Services;

use App\Models\Debtor;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;

class DebtorService
{
    private $csvService;

    public function __construct(CsvService $csvService)
    {
        $this->csvService = $csvService;
    }

    public function processDebtors(): void
    {
        $debtors = $this->retrieveDebtors();

        $this->deleteUnusedEntries($debtors);

        $this->createNewEntries($debtors);
    }

    private function deleteUnusedEntries(array $debtors): void
    {
        $existingEntries = Debtor::lazy()->toArray();

        $mappedDebtorNumbers = collect($debtors)->pluck('Debiteurnummer')->toArray();

        collect($existingEntries)->each(function ($entry) use ($mappedDebtorNumbers) {
            if (!in_array($entry["debtor_number"], $mappedDebtorNumbers)) {
                Debtor::destroy($entry["id"]);
            }
        });
    }

    private function retrieveDebtors(): array
    {
        return $this->csvService->retrieveCSVData('storage/app' . env('SFTP_LOCAL_PATH', '/csv') . '/debiteuren.csv');
    }

    private function createNewEntries(array $debtors): void
    {
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
            array_merge(
                ['debtor_number' => $debtor['Debiteurnummer']],
                $this->mapDebtorData($debtor),
            )
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
