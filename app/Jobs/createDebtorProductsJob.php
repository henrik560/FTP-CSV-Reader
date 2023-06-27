<?php

namespace App\Jobs;

use App\Models\DebtorProduct;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\LazyCollection;

class createDebtorProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $debtorProducts;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $debtorProducts)
    {
        $this->debtorProducts = $debtorProducts;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        LazyCollection::make($this->debtorProducts)->each(function ($product) {
            DebtorProduct::updateOrCreate(
                ['debtor_number' => $product['debtor_number'], 'product_number' => $product['product_number']],
                $product
            );
        });
    }
}
