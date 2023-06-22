<?php

namespace App\Services;

use App\Models\DebtorProduct;
use Illuminate\Support\Facades\Log;

class DebtorProductService
{
    private $csvService;

    public function __construct(CsvService $csvService)
    {
        $this->csvService = $csvService;
    }

    public function registerDebtorProducts()
    {
        $debtorProducts = $this->csvService->retrieveCSVData('storage/app' . env('SFTP_LOCAL_PATH', '/csv') . '/debiteur_artikel.csv', ['debtor_number', 'product_number', 'sale']);

        $this->deleteUnusedEntries($debtorProducts);

        // TODO laravel lazy collection https://laravel.com/docs/10.x/collections#lazy-collections

        // collect($debtorProducts)->chunk(1000)->each(function ($chunk) {
        //     $chunk->each(function ($product) {
        //         if ($existingDebtorProduct = $this->debtorProductExists($product)) {
        //             $this->updateDebtorProduct($existingDebtorProduct, $product);
        //         } else {
        //             $this->createDebtorProduct($product);
        //         }
        //     });
        // });
    }

    private function deleteUnusedEntries(array $debtorProducts): void
    {
        $exisingEntries = DebtorProduct::lazy()->groupBy(['debtor_number', 'product_number'])->toArray();

        $debtorProducts = collect($debtorProducts)->groupBy('debtor_number', 'product_number')->toArray();

        Log::emergency("isset", [$exisingEntries]);
        Log::emergency("entry", [$debtorProducts]);


        collect($exisingEntries)->each(function ($groupEntry, $debtorKey) use ($debtorProducts) {
            collect($groupEntry)->each(function ($entry, $productKey) use ($debtorProducts, $debtorKey) {

                if (isset($debtorProducts[$debtorKey])) {
                    Log::emergency("isset", [$debtorProducts[$debtorKey]]);
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
        return DebtorProduct::where('debtor_number', $product["debtor_number"])->where('product_number', $product["product_number"])->first();
    }
}
