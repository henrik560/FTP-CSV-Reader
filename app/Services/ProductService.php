<?php

namespace App\Services;

use App\Models\Product;

class ProductService
{
    private $csvService;

    function __construct(CsvService $csvService)
    {
        $this->csvService = $csvService;
    }

    public function registerProducts()
    {
        $products = $this->csvService->retrieveCSVData('/artikelen-csv/artikelen.csv');

        collect($products)->chunk(1000)->each(function ($chunk) {
            $chunk->each(function ($product) {
                $product = Product::create([
                    "product_number" => $product["Artikelnummer"],
                    "oms_1" => $product["Oms1"],
                    "oms_2" => $product["Oms2"],
                    "oms_3" => $product["Oms3"],
                    "search_name" => $product["Zoeknaam"],
                    "group" => $product["Groep"],
                    "ean_number" => $product["EAN"],
                    "sell_price" => $product["Verkoopprijs"],
                    "unit" => $product["Eenheid"],
                    "unit_price" => $product["Prijs per"],
                    "stock" => $product["Voorraad"],
                ]);
            });
        });
    }
}
