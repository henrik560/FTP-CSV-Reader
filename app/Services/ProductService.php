<?php

namespace App\Services;

use App\Jobs\createProductsJob;
use App\Models\Product;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\LazyCollection;

class ProductService
{
    private $csvService;

    public function __construct(CsvService $csvService)
    {
        $this->csvService = $csvService;
    }

    public function processProducts(): void
    {
        $products = $this->retrieveProducts();

        $this->deleteUnusedEntries($products);

        $this->createNewEntries($products);
    }

    private function retrieveProducts(): array
    {
        return $this->csvService->retrieveCSVData('storage/app'.env('SFTP_LOCAL_PATH', '/csv').'/artikelen.csv');
    }

    public function createNewEntries(array $products): void
    {
        LazyCollection::make($products)->chunk(env('CHUNK_SIZE', 1000))->each(function ($chunk) {
            Queue::push(new createProductsJob($chunk));
        });
    }

    private function deleteUnusedEntries(array $products): void
    {
        Product::lazy()->each(function ($entry) use ($products) {
            if (! in_array($entry['product_number'], array_column($products, 'Artikelnummer'))) {
                $entry->delete();
            }
        });
    }
}
