<?php

namespace App\Services;

use App\Jobs\deleteDebtorProductsJob;
use App\Jobs\updateOrCreateDebtorProductsJob;
use App\Models\DebtorProduct;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
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
        return $this->csvService->retrieveCSVData('storage/app' . env('SFTP_LOCAL_PATH', '/csv') . '/debiteur_artikel.csv', ['debtor_number', 'product_number', 'sale'], ['debtor_number']);
    }

    private function createNewEntries(array $debtorProducts): void
    {
        LazyCollection::make($debtorProducts)->each(function ($debtorProduct) {
            LazyCollection::make($debtorProduct)->chunk(env('CHUNK_SIZE', 1000))->each(function ($debtorProductsChunk) {
                Queue::push(new updateOrCreateDebtorProductsJob($debtorProductsChunk->toArray()));
            });
        });
    }

    private function deleteUnusedEntries(array $debtorProducts): void
    {
        LazyCollection::make($this->getUnusedEntryIds($debtorProducts))->chunk(env('CHUNK_SIZE', 1000))->each(function ($debtorProductIdsChunk) {
            Queue::push(new deleteDebtorProductsJob($debtorProductIdsChunk->toArray()));
        });
    }

    private function getUnusedEntryIds(array $debtorProducts): array
    {
        return DebtorProduct::lazy()->filter(function ($debtorProduct) use ($debtorProducts) {
            if (isset($debtorProducts[$debtorProduct['debtor_number']])) {
                return;
            }

            return !isset($debtorProducts[$debtorProduct['debtor_number']]) || !in_array($debtorProduct['product_number'], array_column($debtorProducts[$debtorProduct['debtor_number']], 'product_number'));
        })->map(function ($debtorProduct) {
            return $debtorProduct['id'];
        })->toArray();
    }
}
