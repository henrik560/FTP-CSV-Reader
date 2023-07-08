<?php

namespace App\Services;

use App\Jobs\deleteDebtorNettosJob;
use App\Jobs\deleteDebtorsJob;
use App\Jobs\updateOrCreateDebtorsJob;
use App\Models\Debtor;
use App\Models\DebtorNetto;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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
        $debtors = $this->retrieveFileContent('/debiteuren.csv');

        $this->deleteUnusedDebtorEntries($debtors);

        $this->createNewDebtorEntries($debtors);
    }

    public function processDebtorNettos(): void
    {
        $debtorNettos = $this->retrieveFileContent('/debiteur_netto.csv');

        $this->deleteUnusedDebtorNettoEntries($debtorNettos);

        $this->createNewDebtorNettoEntries($debtorNettos);
    }

    private function retrieveFileContent(string $filename): array
    {
        return $this->csvService->retrieveCSVData('storage/app' . env('SFTP_LOCAL_PATH', '/csv') . $filename);
    }

    private function createNewDebtorNettoEntries(array $debtorNettos): void
    {
        LazyCollection::make($debtorNettos)->chunk(env('CHUNK_SIZE', 1000))->each(function ($debtorNettosChunk) {
            Queue::push(new updateOrCreateDebtorsJob($debtorNettosChunk->toArray()));
        });
    }

    private function createNewDebtorEntries(array $debtors): void
    {
        LazyCollection::make($debtors)->chunk(env('CHUNK_SIZE', 1000))->each(function ($debtorsChunk) {
            Queue::push(new updateOrCreateDebtorsJob($debtorsChunk->toArray()));
        });
    }

    private function deleteUnusedDebtorEntries(array $debtors): void
    {
        LazyCollection::make($this->getUnusedDebtorEntryIds($debtors))->chunk(env('CHUNK_SIZE', 1000))->each(function ($debtorIdsChunk) {
            Queue::push(new deleteDebtorsJob($debtorIdsChunk->toArray()));
        });
    }

    private function deleteUnusedDebtorNettoEntries(array $debtorNettos): void
    {
        LazyCollection::make($this->getUnusedDebtorNettoEntryIds($debtorNettos))->chunk(env('CHUNK_SIZE', 1000))->each(function ($debtorNettoIdsChunk) {
            Queue::push(new deleteDebtorNettosJob($debtorNettoIdsChunk->toArray()));
        });
    }

    private function getUnusedDebtorEntryIds(array $debtors): array
    {
        return Debtor::lazy()->filter(function ($debtor) use ($debtors) {
            return !in_array($debtor['debtor_number'], array_column($debtors, 'Debiteurnummer'));
        })->map(function ($debtor) {
            return $debtor['id'];
        })->toArray();
    }

    private function getUnusedDebtorNettoEntryIds(array $debtorNettos): array
    {
        return DebtorNetto::lazy()->filter(function ($debtorNetto) use ($debtorNettos) {
            return !in_array($debtorNetto['debtor_number'], array_column($debtorNettos, 'Debiteurnummer')) &&
                !in_array($debtorNetto['product_number'], array_column($debtorNettos, 'Artikelnummer')) &&
                !in_array($debtorNetto['type'], array_column($debtorNettos, 'Type')) &&
                !in_array($debtorNetto['pbk'], array_column($debtorNettos, 'P/B/K'));
        })->map(function ($debtorNetto) {
            return $debtorNetto['id'];
        })->toArray();
    }

    public function updatePassword(string $password, Debtor $debtor): void
    {
        $debtor->fill(["password" => Hash::make($password)])->save();
    }
}
