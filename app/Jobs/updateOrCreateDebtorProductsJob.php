<?php

namespace App\Jobs;

use App\Models\DebtorProduct;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;

class updateOrCreateDebtorProductsJob implements ShouldQueue
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
            if (!isset($product['debtor_number']) || !isset($product['product_number']) || !isset($product['sale'])) {
                return;
            }

            try {
                DebtorProduct::updateOrCreate(
                    ['debtor_number' => $product['debtor_number'], 'product_number' => $product['product_number']],
                    $product
                );
            } catch (\Exception $e) {
                Log::emergency('Error in debtorProductsJob', [
                    $e->getMessage(),
                ]);
            }
        });
    }
}
