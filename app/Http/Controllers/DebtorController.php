<?php

namespace App\Http\Controllers;

use App\Models\Debtor;
use App\Models\DebtorProduct;
use App\Services\DebtorService;
use App\Services\PaginationService;
use App\Services\ValidateRequestService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class DebtorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(PaginationService $paginationService, Request $request)
    {
        $pageSize = $paginationService->validatePageSize($request->input('pageSize'));

        $query = Debtor::query();

        if ($q = $request->input('q')) {
            $query->where('debtor_number', 'LIKE', "%{$q}%")
                ->orWhere('name_1', 'LIKE', "%{$q}%")
                ->orWhere('search_name', 'LIKE', "%{$q}%");
        }

        return $query->paginate($pageSize);
    }

    public function show(string $debtorId)
    {
        return Debtor::where('debtor_number', '=', $debtorId)->firstOrFail();
    }

    public function delete(Request $request, string $id, DebtorService $debtorService, ValidateRequestService $validateRequestService)
    {
        $validateRequestService->validateRequest(
            array_merge(["id" => $id], ["password" => $request->get('password')]),
            ['id' => 'required', 'password' => 'required']
        );

        if (!$debtorService->deleteDebtorByIdAndPassword($id, $request->get('password'))) {
            return response()->json(["error" => "Invalid credentials/request"], Response::HTTP_BAD_REQUEST);
        }

        return response()->json(["message" => "Debtor succesfully deleted"], Response::HTTP_ACCEPTED);
    }

    public function products(PaginationService $paginationService, Request $request, string $debtorNumber)
    {
        $pageSize = $paginationService->validatePageSize($request->input('pageSize'));

        $debtor = Debtor::where('debtor_number', '=', $debtorNumber)->firstOrFail();

        $products = DebtorProduct::with('product')->where('debtor_number', '=', $debtorNumber);

        if ($q = $request->input('q')) {
            $products->where('search_name', 'LIKE', "%{$q}%")
                ->orWhere('product_number', 'LIKE', "%{$q}%")
                ->orWhere('group', 'LIKE', "%{$q}%")
                ->orWhere('oms_1', 'LIKE', "%{$q}%")
                ->orWhere('oms_2', 'LIKE', "%{$q}%")
                ->orWhere('oms_3', 'LIKE', "%{$q}%");
        }

        return ['debtor' => $debtor, 'products' => $products->paginate($pageSize)];
    }
}
