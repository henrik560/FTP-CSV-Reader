<?php

namespace App\Services;

use App\Jobs\deleteDebtorsJob;
use App\Jobs\updateOrCreateDebtorsJob;
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
        return $this->csvService->retrieveCSVData('storage/app' . env('SFTP_LOCAL_PATH', '/csv') . '/debiteuren.csv');
    }

    private function createNewEntries(array $debtors): void
    {
        LazyCollection::make($debtors)->chunk(env('CHUNK_SIZE', 1000))->each(function ($debtorsChunk) {
            Queue::push(new updateOrCreateDebtorsJob($debtorsChunk->toArray()));
        });
    }

    private function deleteUnusedEntries(array $debtors): void
    {
        LazyCollection::make($this->getUnusedEntryIds($debtors))->chunk(env('CHUNK_SIZE', 1000))->each(function ($debtorIdsChunk) {
            Queue::push(new deleteDebtorsJob($debtorIdsChunk->toArray()));
        });
    }

    private function getUnusedEntryIds(array $debtors): array
    {
        return Debtor::lazy()->filter(function ($debtor) use ($debtors) {
            return !in_array($debtor['debtor_number'], array_column($debtors, 'Debiteurnummer'));
        })->map(function ($debtor) {
            return $debtor['id'];
        })->toArray();
    }

    /** @param App\http\controller\authenticationController used in --> */
    // TODO generate secure password and api token for user and send notification
    private function generatePassword(Debtor $debtor): void
    {
    }
}
