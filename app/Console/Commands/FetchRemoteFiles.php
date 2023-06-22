<?php

namespace App\Console\Commands;

use App\Jobs\FetchRemoteFilesJob as FetchRemoteFilesJob;
use Illuminate\Console\Command;

class FetchRemoteFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manually run the cron job to fetch files from the ftp server';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        FetchRemoteFilesJob::dispatch();

        return Command::SUCCESS;
    }
}
