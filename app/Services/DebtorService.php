<?php

namespace App\Services;

use App\Jobs\createDebtorsJob;
use App\Models\Debtor;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\LazyCollection;

class DebtorService
{
    private $csvService;

    public function __construct(CsvService $csvService)
    {
        $this->csvService = $csvService;
    }

    public function processDebtors(): void
    {
        $debtors = $this->retrieveDebtors();

        $this->deleteUnusedEntries($debtors);

        $this->createNewEntries($debtors);
    }

    private function retrieveDebtors(): array
    {
        return $this->csvService->retrieveCSVData('storage/app'.env('SFTP_LOCAL_PATH', '/csv').'/debiteuren.csv');
    }

    private function createNewEntries(array $debtors): void
    {
        LazyCollection::make($debtors)->chunk(env('CHUNK_SIZE', 1000))->each(function ($debtor) {
            Queue::push(new createDebtorsJob($debtor));
        });
    }

    private function deleteUnusedEntries(array $debtors): void
    {
        Debtor::lazy()->each(function ($debtor) use ($debtors) {
            if (! in_array($debtor['debtor_number'], array_column($debtors, 'Debiteurnummer'))) {
                $debtor->delete();
            }
        });
    }
}
