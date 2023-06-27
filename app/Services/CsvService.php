<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class CsvService
{

    public function retrieveCSVData(string $csvPath, array $columnNames = null, array|string $groupBy = null): array
    {
        abort_if(!file_exists(base_path($csvPath)), 404);

        $file = fopen(base_path($csvPath), 'r');

        if (is_null($columnNames)) {
            $columnNames = $this->retrieveColumnNames($file);
        }

        $aggregatedData = $this->processCSVData($file, $columnNames);
        fclose($file);

        if (!is_null($groupBy)) {
            $aggregatedData = $this->groupByData($aggregatedData, $groupBy);
        }

        return $aggregatedData;
    }

    private function retrieveColumnNames($file): array
    {
        $headerRow = fgetcsv($file, 0, ';');
        return collect($headerRow)->map(function ($column) {
            return str_replace(',', '', $column);
        })->toArray();
    }

    private function processCSVData($file, $columnNames): array
    {
        $aggregatedData = [];
        $i = 0;

        while (($row = fgetcsv($file, 0)) !== false) {
            $row = explode(';', implode('', str_replace('"', '', $row)));
            $encoded = mb_convert_encoding(implode(';', $row), 'UTF-8', 'ISO-8859-1');
            $convertedRow = collect(explode(';', $encoded))->map(function ($item) {
                return trim($item);
            })->toArray();

            if (count($convertedRow) == count($columnNames)) {
                $aggregatedData[] = array_combine($columnNames, $convertedRow);
            }
        }

        return $aggregatedData;
    }

    private function groupByData($data, $groupBy): array
    {
        $groupByColumns = is_array($groupBy) ? $groupBy : [$groupBy];

        return collect($data)->groupBy($groupByColumns)->toArray();
    }
}
