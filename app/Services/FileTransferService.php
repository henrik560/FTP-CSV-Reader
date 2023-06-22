<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class FileTransferService
{
    public function transferFiles(): void
    {
        $this->moveExisitingFiles();

        $this->downloadRemoteFiles();
    }

    private function downloadRemoteFiles(): void
    {
        collect(explode(',', env('SFTP_REMOTE_FILES', "")))->each(function ($file) {
            $this->downloadFile($file);
        });
    }


    private function downloadFile(string $fileName): void
    {
        if (!Storage::disk('sftp')->exists($fileName)) {
            return;
        }

        Storage::disk('local')->put($fileName, Storage::disk('sftp')->get($fileName));
    }

    private function moveExisitingFiles(): void
    {
        $files = Storage::files();

        if (empty($files)) {
            return;
        }

        $currentDateTime = Carbon::now()->format('Y-m-d_H-i-s');

        collect($files)->each(function ($file) use ($currentDateTime) {
            Storage::move($file, "$currentDateTime/$file");
        });
    }
}
