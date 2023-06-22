<?php

namespace App\Jobs;

use App\Services\DebtorProductService;
use App\Services\DebtorService;
use App\Services\FileTransferService;
use App\Services\ProductService;
use Illuminate\Bus\Queueable;
use Illuminate\Console\Command;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchRemoteFilesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(FileTransferService $fileTransferService, DebtorProductService $debtorProductService, DebtorService $debtorService, ProductService $productService)
    {
        // $fileTransferService->transferFiles();

        $this->processData($debtorService, $debtorProductService, $productService);

        return Command::SUCCESS;
    }

    private function processData(DebtorService $debtorService, DebtorProductService $debtorProductService, ProductService $productService): void
    {
        // $debtorService->registerDebtors();
        $debtorProductService->registerDebtorProducts();
        // $productService->registerProducts();
    }
}
