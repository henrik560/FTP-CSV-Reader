<?php

namespace App\Services;

use App\Models\DebtorProduct;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;

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
        return $this->csvService->retrieveCSVData('storage/app' . env('SFTP_LOCAL_PATH', '/csv') . '/debiteur_artikel.csv', ['debtor_number', 'product_number', 'sale']);
    }

    private function createNewEntries(array $debtorProducts): void
    {
        ini_set('memory_limit', '2G');

        LazyCollection::make(function () use ($debtorProducts) {
            yield from collect($debtorProducts)->chunk(10);
        })->each(function ($groupedProducts) {
            $groupedProducts->each(function ($product) {
                DebtorProduct::updateOrCreate(
                    ['debtor_number' => $product["debtor_number"], "product_number" => $product["product_number"]],
                    $product
                );
            });
        });
    }

    private function deleteUnusedEntries(array $debtorProducts): void
    {
        $existingEntries = DebtorProduct::lazy()->groupBy(['debtor_number', 'product_number'])->toArray();

        $debtorProductsGrouped = LazyCollection::make(function () use ($debtorProducts) {
            yield from collect($debtorProducts)->groupBy(['debtor_number', 'product_number']);
        })->toArray();

        LazyCollection::make(function () use ($existingEntries, $debtorProductsGrouped) {
            foreach ($existingEntries as $debtorKey => $groupEntry) {
                yield [$debtorKey, $groupEntry, $debtorProductsGrouped[$debtorKey] ?? null];
            }
        })->each(function ($data) {
            [$debtorKey, $groupEntry, $mappedProductNumbers] = $data;

            foreach ($groupEntry as $productKey => $entry) {
                if (is_null($mappedProductNumbers) || !in_array($productKey, $mappedProductNumbers)) {
                    $entryId = collect($entry)->first()['id'];
                    DebtorProduct::destroy($entryId);
                }
            }
        });
    }
}
