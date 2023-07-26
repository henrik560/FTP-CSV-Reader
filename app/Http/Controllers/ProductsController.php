<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductSort;
use App\Services\PaginationService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductsController extends Controller
{
    public function index(PaginationService $paginationService, Request $request): LengthAwarePaginator
    {
        $pageSize = $paginationService->validatePageSize($request->input('pageSize'));

        $query = ProductSort::with(["groupImageRelationship", "products"]);

        return $query->paginate($pageSize);
    }

    public function show(PaginationService $paginationService, Request $request, string $group): LengthAwarePaginator
    {
        $pageSize = $paginationService->validatePageSize($request->input('pageSize'));

        $query = ProductSort::with(["groupImageRelationship", "products"])->where('group', $group);

        return $query->paginate($pageSize);
    }
}
