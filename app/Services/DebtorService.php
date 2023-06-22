<?php

namespace App\Services;

use App\Models\Debtor;
use App\Models\DebtorProduct;

class DebtorService
{
    private $csvService;

    function __construct(CsvService $csvService)
    {
        $this->csvService = $csvService;
    }

    public function registerDebtors()
    {
        $debtors = $this->csvService->retrieveCSVData('/artikelen-csv/debiteuren.csv');

        collect($debtors)->each(function ($debtor) {
            $debtor = Debtor::create([
                "debtor_number" => $debtor["Debiteurnummer"],
                "name_1" => $debtor["Naam1"],
                "name_2" => $debtor["Naam2"],
                "search_name" => $debtor["Zoeknaam"],
                "address" => $debtor["Adres"],
                "postalcode" => $debtor["Postcode"],
                "city" => $debtor["Plaats"],
                "country" => $debtor["Land"],
                "contact" => $debtor["Contact"],
                "phonenumber" => $debtor["Telefoon"],
                "mobile" => $debtor["Mobiel"],
                "email" => $debtor["Email"],
                "email_cc" => $debtor["Email cc"],
                "email_invoice" => $debtor["Email factuur"],
                "email_invoice_cc" => $debtor["Email factuur cc"],
                "tax_number" => $debtor["BTW nummer"],
            ]);
        });
    }

    public function registerDebtorProducts()
    {
        $debtorProducts = $this->csvService->retrieveCSVData('/artikelen-csv/debiteur_artikel.csv', ["debtor_number", "product_number", "sale"]);

        collect($debtorProducts)->chunk(1000)->each(function ($chunk) {
            $chunk->each(function ($product) {
                $product = DebtorProduct::firstOrCreate($product);
            });
        });
    }
}
