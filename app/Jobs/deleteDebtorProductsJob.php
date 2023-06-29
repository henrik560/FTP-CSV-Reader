<?php

namespace App\Jobs;

use App\Models\DebtorProduct;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class deleteDebtorProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $debtorProductIds;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $ids)
    {
        $this->debtorProductIds = $ids;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        DebtorProduct::destroy($this->debtorProductIds);
    }
}
