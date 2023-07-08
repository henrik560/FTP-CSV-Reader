<?php

namespace App\Jobs;

use App\Models\ProductSort;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class deleteProductSortsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $productSortIds;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $ids)
    {
        $this->productSortIds = $ids;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        ProductSort::destroy($this->productSortIds);
    }
}
