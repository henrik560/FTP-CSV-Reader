<?php

namespace App\Services;

use App\Models\DebtorProduct;
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
        return $this->csvService->retrieveCSVData('storage/app'.env('SFTP_LOCAL_PATH', '/csv').'/debiteur_artikel.csv', ['debtor_number', 'product_number', 'sale']);
    }

    private function createNewEntries(array $debtorProducts): void
    {
        LazyCollection::make(function () use ($debtorProducts) {
            yield from collect($debtorProducts)->chunk(1000);
        })->each(function ($chunk) {
            $chunk->each(function ($product) {
                if ($existingDebtorProduct = $this->debtorProductExists($product)) {
                    $this->updateDebtorProduct($existingDebtorProduct, $product);
                } else {
                    $this->createDebtorProduct($product);
                }
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

            collect($groupEntry)->each(function ($entry, $productKey) use ($mappedProductNumbers) {
                if (is_null($mappedProductNumbers) || ! in_array($productKey, $mappedProductNumbers)) {
                    $entryId = collect($entry)->first()['id'];
                    DebtorProduct::destroy($entryId);
                }
            });
        });
    }

    private function updateDebtorProduct(DebtorProduct $existingDebtorProduct, array $debtorProduct): void
    {
        $existingDebtorProduct->update($debtorProduct);
    }

    private function createDebtorProduct(array $debtorProduct): void
    {
        DebtorProduct::create($debtorProduct);
    }

    private function debtorProductExists(array $product): ?DebtorProduct
    {
        return DebtorProduct::where('debtor_number', $product['debtor_number'])->where('product_number', $product['product_number'])->first();
    }
}
