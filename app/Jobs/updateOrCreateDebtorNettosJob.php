<?php

namespace App\Jobs;

use App\Models\DebtorNetto;
use App\Models\DebtorProduct;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
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
            if (
                !isset($debtorNetto['Debiteurnummer']) ||
                !isset($debtorNetto['Artikelnummer']) ||
                !isset($debtorNetto["Type"]) ||
                !isset($debtorNetto["P/B/K"])
            ) {
                return;
            }

            try {
                DebtorNetto::updateOrCreate(
                    ['debtor_number' => $debtorNetto['Debiteurnummer'], 'product_number' => $debtorNetto['Artikelnummer'], "type" => $debtorNetto["Type"], "pbk" => $debtorNetto["P/B/K"]],
                    $debtorNetto
                );
            } catch (\Exception $e) {
                Log::emergency('Error in debtorNettosJob', [
                    $e->getMessage(),
                ]);
            }
        });
    }
}
