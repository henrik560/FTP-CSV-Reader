<?php

namespace App\Jobs;

use App\Models\ProductSort;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;

class updateOrCreateProductSortsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $productSorts;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $productSorts)
    {
        $this->productSorts = $productSorts;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        LazyCollection::make($this->productSorts)->each(function ($productSort) {
            if (!isset($productSort['Laag']) || !isset($productSort['Groep']) || !isset($productSort['Volgnummer'])) {
                return;
            }

            try {
                ProductSort::updateOrCreate(
                    ['layer' => $productSort['Laag'], 'group' => $productSort["Groep"], 'serial_number' => $productSort["Volgnummer"]],
                    $this->mapProductSortData($productSort)
                );
            } catch (\Exception $e) {
                Log::emergency('Error in ProductsSortJob', [
                    $e->getMessage(),
                ]);
            }
        });
    }

    private function mapProductSortData(array $productSort): array
    {
        return [
            'layer' => $productSort["Laag"],
            'group' => $productSort['Groep'],
            'serial_number' => $productSort['Volgnummer']
        ];
    }
}
