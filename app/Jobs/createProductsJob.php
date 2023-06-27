<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\LazyCollection;

class createProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $products;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $products)
    {
        $this->products = $products;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        LazyCollection::make($this->products)->each(function ($product) {
            Product::updateOrCreate(
                ['product_number' => $product["Artikelnummer"]],
                $this->mapProductData($product)
            );
        });
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
