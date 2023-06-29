<?php

namespace App\Services;

use App\Jobs\deleteProductsJob;
use App\Jobs\updateOrCreateProductsJob;
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
        LazyCollection::make($products)->chunk(env('CHUNK_SIZE', 1000))->each(function ($productsChunk) {
            Queue::push(new updateOrCreateProductsJob($productsChunk->toArray()));
        });
    }

    private function deleteUnusedEntries(array $products): void
    {
        LazyCollection::make($this->getUnusedEntryIds($products))->chunk(env('CHUNK_SIZE', 1000))->each(function ($productIdsChunk) {
            Queue::push(new deleteProductsJob($productIdsChunk->toArray()));
        });
    }

    private function getUnusedEntryIds(array $products): array
    {
        return Product::lazy()->filter(function ($product) use ($products) {
            return ! in_array($product['product_number'], array_column($products, 'Artikelnummer'));
        })->map(function ($product) {
            return $product['id'];
        })->toArray();
    }
}
