<?php

namespace App\Services;

use App\Jobs\createDebtorProductJob;
use App\Models\DebtorProduct;
use Illuminate\Support\Facades\Queue;

class DebtorProductService
{
    private $csvService;

    public function __construct(CsvService $csvService)
    {
        $this->csvService = $csvService;
    }

    public function processDebtorProducts(): void
    {
        $debtorProducts = $this->retrieveDebtorProducts();

        $this->deleteUnusedEntries($debtorProducts);

        $this->createNewEntries($debtorProducts);
    }

    private function retrieveDebtorProducts(): array
    {
        return $this->csvService->retrieveCSVData('storage/app' . env('SFTP_LOCAL_PATH', '/csv') . '/debiteur_artikel.csv', ['debtor_number', 'product_number', 'sale'], ["debtor_number"]);
    }

    private function createNewEntries(array $debtorProducts): void
    {
        collect($debtorProducts)->each(function ($debtorProduct) {
            Queue::push(new createDebtorProductJob($debtorProduct));
        });
    }

    private function deleteUnusedEntries(array $debtorProducts): void
    {
        DebtorProduct::lazy()->each(function ($entry) use ($debtorProducts) {
            $debtorProduct = $entry->toArray();

            if (!isset($debtorProducts[$debtorProduct["debtor_number"]]) || !in_array($debtorProduct["product_number"], array_column($debtorProducts[$debtorProduct["debtor_number"]], "product_number"))) {
                $entry->delete();
            }
        });
    }
}
