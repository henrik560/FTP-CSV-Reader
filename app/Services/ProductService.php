<?php

namespace App\Services;

use App\Jobs\deleteProductsJob;
use App\Jobs\deleteProductSortsJob;
use App\Jobs\updateOrCreateProductsJob;
use App\Jobs\updateOrCreateProductSortsJob;
use App\Models\Product;
use App\Models\ProductSort;
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
        $products = $this->retrieveFileContent('/artikelen.csv');

        $this->deleteUnusedProductEntries($products);

        $this->createNewProductEntries($products);
    }

    public function processProductSorts(): void
    {
        $productSorts = $this->retrieveFileContent('/artikel_sorteer.csv');

        $this->deleteUnusedProductSortEntries($productSorts);

        $this->createNewProductSortEntries($productSorts);
    }

    private function retrieveFileContent(string $filename): array
    {
        return $this->csvService->retrieveCSVData('storage/app' . env('SFTP_LOCAL_PATH', '/csv') . $filename);
    }

    private function createNewProductSortEntries(array $productSorts): void
    {
        LazyCollection::make($productSorts)->chunk(env('CHUNK_SIZE', 1000))->each(function ($productSortsChunk) {
            Queue::push(new updateOrCreateProductSortsJob($productSortsChunk->toArray()));
        });
    }

    private function createNewProductEntries(array $products): void
    {
        LazyCollection::make($products)->chunk(env('CHUNK_SIZE', 1000))->each(function ($productsChunk) {
            Queue::push(new updateOrCreateProductsJob($productsChunk->toArray()));
        });
    }

    private function deleteUnusedProductEntries(array $products): void
    {
        LazyCollection::make($this->getUnusedProductEntryIds($products))->chunk(env('CHUNK_SIZE', 1000))->each(function ($productIdsChunk) {
            Queue::push(new deleteProductsJob($productIdsChunk->toArray()));
        });
    }

    private function deleteUnusedProductSortEntries(array $productSorts): void
    {
        LazyCollection::make($this->getUnusedProductSortEntryIds($productSorts))->chunk(env('CHUNK_SIZE', 1000))->each(function ($productSortsIdsChunk) {
            Queue::push(new deleteProductSortsJob($productSortsIdsChunk->toArray()));
        });
    }

    private function getUnusedProductSortEntryIds(array $productSorts): array
    {
        return ProductSort::lazy()->filter(function ($productSort) use ($productSorts) {
            return !in_array($productSort['layer'], array_column($productSorts, 'Laag')) &&
                !in_array($productSort['group'], array_column($productSorts, 'Groep')) &&
                !in_array($productSort['serial_number'], array_column($productSorts, 'Volgnummer'));
        })->map(function ($product) {
            return $product['id'];
        })->toArray();
    }

    private function getUnusedProductEntryIds(array $products): array
    {
        return Product::lazy()->filter(function ($product) use ($products) {
            return !in_array($product['product_number'], array_column($products, 'Artikelnummer'));
        })->map(function ($product) {
            return $product['id'];
        })->toArray();
    }
}
