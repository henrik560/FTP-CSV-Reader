<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Log;

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

        // $this->createNewEntries($products);
    }

    private function retrieveProducts(): array
    {
        return $this->csvService->retrieveCSVData('storage/app' . env('SFTP_LOCAL_PATH', '/csv') . '/artikelen.csv');
    }

    private function deleteUnusedEntries(array $products): void
    {
        $existingEntries = Product::lazy()->toArray();

        $mappedProductNumbers = collect($products)->pluck('Artikelnummer')->toArray();

        collect($existingEntries)->each(function ($entry) use ($mappedProductNumbers) {
            if (!in_array($entry["product_number"], $mappedProductNumbers)) {
                Product::destroy($entry["id"]);
            }
        });
    }

    public function createNewEntries(array $products): void
    {
        collect($products)->chunk(1000)->each(function (object $chunk) {
            $chunk->each(function (array $product) {
                if ($existingProduct = $this->productExists($product['Artikelnummer'])) {
                    $this->updateProduct($existingProduct, $product);
                } else {
                    $this->registerNewProduct($product);
                }
            });
        });
    }

    private function updateProduct(Product $existingProduct, array $product): void
    {
        $existingProduct->update($this->mapProductData($product));
    }

    private function registerNewProduct(array $product): void
    {
        Product::create(
            array_merge(
                ['product_number' => $product['Artikelnummer']],
                $this->mapProductData($product),
            )
        );
    }

    private function productExists(string $productNumber): ?Product
    {
        return Product::where('product_number', $productNumber)->first();
    }

    private function mapProductData(array $product): array
    {
        return [
            'oms_1' => $product['Oms1'],
            'oms_2' => $product['Oms2'],
            'oms_3' => $product['Oms3'],
            'search_name' => $product['Zoeknaam'],
            'group' => $product['Groep'],
            'ean_number' => $product['EAN'],
            'sell_price' => $product['Verkoopprijs'],
            'unit' => $product['Eenheid'],
            'unit_price' => $product['Prijs per'],
            'stock' => $product['Voorraad'],
        ];
    }
}
