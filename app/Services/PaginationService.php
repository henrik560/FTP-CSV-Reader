<?php

namespace App\Services;

use Symfony\Component\HttpKernel\Exception\HttpException;

class PaginationService
{
    public function validatePageSize($pageSize): int
    {
        if (is_null($pageSize)) {
            return 32;
        }

        if (! is_numeric($pageSize)) {
            throw new HttpException(400, 'Invalid pageSize parameter');
        }

        $pageSize = (int) $pageSize;

        if (! is_numeric($pageSize)) {
            throw new HttpException(400, 'pageSize must be between 1 and 999');
        }

        abort_if($pageSize <= 0 || $pageSize > 999, 400);

        return $pageSize;
    }
}
