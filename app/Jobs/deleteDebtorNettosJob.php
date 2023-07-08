<?php

namespace App\Jobs;

use App\Models\DebtorNetto;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class deleteDebtorNettosJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $debtorNettoIds;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $ids)
    {
        $this->debtorNettoIds = $ids;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        DebtorNetto::destroy($this->debtorNettoIds);
    }
}
