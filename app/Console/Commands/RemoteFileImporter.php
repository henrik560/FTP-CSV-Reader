<?php

namespace App\Console\Commands;

use App\Services\DebtorProductService;
use App\Services\DebtorService;
use App\Services\FileTransferService;
use App\Services\ProductService;
use Illuminate\Console\Command;

class RemoteFileImporter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'importer:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch files from an sftp server and upload the data to the database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(FileTransferService $fileTransferService, DebtorProductService $debtorProductService, DebtorService $debtorService, ProductService $productService)
    {
        $fileTransferService->transferFiles();

        $this->processData($debtorService, $debtorProductService, $productService);

        return Command::SUCCESS;
    }

    private function processData(DebtorService $debtorService, DebtorProductService $debtorProductService, ProductService $productService): void
    {
        $debtorService->processDebtors();

        $debtorService->processDebtorNettos();

        $debtorProductService->processDebtorProducts();

        $productService->processProducts();

        $productService->processProductSorts();
    }
}
