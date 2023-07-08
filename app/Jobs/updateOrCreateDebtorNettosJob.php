<?php

namespace App\Jobs;

use App\Models\DebtorProduct;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\LazyCollection;

class updateOrCreateDebtorNettosJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $debtorNettos;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $debtorNettos)
    {
        $this->debtorNettos = $debtorNettos;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        LazyCollection::make($this->debtorNettos)->each(function ($debtorNetto) {
            DebtorProduct::updateOrCreate(
                ['debtor_number' => $debtorNetto['debtor_number'], 'product_number' => $debtorNetto['product_number'], "type" => $debtorNetto["type"], "pbk" => $debtorNetto["pbk"]],
                $debtorNetto
            );
        });
    }
}
