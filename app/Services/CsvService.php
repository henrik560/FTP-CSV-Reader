<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class CsvService
{
    public function retrieveCSVData(string $csvPath, array $columns = null)
    {
        [$data, $columns] = $this->aggregateCSVData($csvPath, $columns);

        $csvData = collect($data)->map(function ($dataRow) use ($columns) {
            return array_combine($columns, $dataRow);
        })->toArray();

        return $csvData;
    }

    public function aggregateCSVData(string $csvPath, array $columnNames = null): array
    {
        abort_if(!file_exists(base_path($csvPath)), 404);

        $file = fopen(base_path($csvPath), 'r');

        if (is_null($columnNames)) {
            $columnNames = collect(fgetcsv($file, 0, ';'))->map(function ($column) {
                return str_replace(",", '', $column);
            })->toArray();
        }

        $aggregatedData = new Collection();

        while (($row = fgetcsv($file, 0)) !== false) {
            $row = explode(';', implode('', str_replace('"', '', $row)));
            $encoded = mb_convert_encoding(implode(';', $row), 'UTF-8', 'ISO-8859-1');
            $convertedRow = collect(explode(';', $encoded))->map(function ($item) {
                $convertedItem = trim($item);

                return $convertedItem;
            })->toArray();

            if (count($convertedRow) == count($columnNames)) {
                $aggregatedData->push($convertedRow);
            }
        }

        fclose($file);

        return [
            $aggregatedData->toArray(),
            $columnNames,
        ];
    }
}
