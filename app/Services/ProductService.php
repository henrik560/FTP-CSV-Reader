<?php

namespace App\Services;

use App\Models\Product;

class ProductService
{
    private $csvService;

    public function __construct(CsvService $csvService)
    {
        $this->csvService = $csvService;
    }

    public function registerProducts()
    {
        $products = $this->csvService->retrieveCSVData('storage/app' . env('SFTP_LOCAL_PATH', '/csv') . '/artikelen.csv');

        // TODO check if a product is in the database that should not be in the database
        collect($products)->chunk(1000)->each(function (object $chunk) {
            $chunk->each(function (array $product) {
                if ($existingProduct = $this->productExists($product["Artikelnummer"])) {
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
