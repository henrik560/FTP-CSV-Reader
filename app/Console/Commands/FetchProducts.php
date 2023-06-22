<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FetchProducts extends Command
{
    protected $signature = 'fetch:products';

    protected $description = 'Fetches product CSV files from SFTP server.';

    public function handle()
    {
        $this->moveExisitingFiles();

        return Command::FAILURE;

        $files = collect(explode(',', env('SFTP_REMOVE_FILES', [])))->each(function ($file) {
            $this->downloadFile($file);
        });
    }

    private function downloadFile(string $fileName): bool
    {
        if (! Storage::disk('sftp')->exists($fileName)) {
            return false;
        }

        Storage::disk('local')->put(
            "$fileName.csv",
            Storage::disk('sftp')->get($fileName)
        );

        return true;
    }

    private function moveExisitingFiles(): void
    {
        $files = Storage::allFiles();

        $currentDateTime = Carbon::now()->format('Y-m-d_H-i-s');
        $newDirectory = Storage::createDirectory($currentDateTime);

        if (! Storage::directoryExists($currentDateTime)) {
            return;
        }

        collect($files)->each(function ($file) use ($currentDateTime) {
            Storage::move($file, "$currentDateTime/$file");
        });
    }
}
