<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\PaginationService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductsController extends Controller
{
    public function index(PaginationService $paginationService, Request $request): LengthAwarePaginator
    {
        $pageSize = $paginationService->validatePageSize($request->input('pageSize'));

        $query = Product::query();

        if ($q = $request->input('q')) {
            $query->where('search_name', 'LIKE', "%{$q}%")
                ->orWhere('product_number', 'LIKE', "%{$q}%")
                ->orWhere('group', 'LIKE', "%{$q}%")
                ->orWhere('oms_1', 'LIKE', "%{$q}%")
                ->orWhere('oms_2', 'LIKE', "%{$q}%")
                ->orWhere('oms_3', 'LIKE', "%{$q}%");
        }

        return $query->paginate($pageSize);
    }
}
