<?php

namespace App\Services;

use App\Jobs\deleteDebtorsJob;
use App\Jobs\updateOrCreateDebtorsJob;
use App\Models\Debtor;
use App\Notifications\PasswordCreatedNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Facades\Notification;

class DebtorService
{
    private $csvService;
    private $passwordService;

    public function __construct(CsvService $csvService, PasswordService $passwordService)
    {
        $this->csvService = $csvService;
        $this->passwordService = $passwordService;
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
    public function generatePassword(Debtor $debtor): void
    {
        $password = $this->passwordService->generate();

        $debtor->fill(['password' => Hash::make($password)]);

        $this->notifyPasswordCreation($password, $debtor);
    }

    private function notifyPasswordCreation(string $password, Debtor $debtor): void
    {
        if (isset($debtor->email) && !is_null($debtor->email)) {
            Notification::send($debtor->email, new PasswordCreatedNotification($password, $debtor));
        }
    }
}
