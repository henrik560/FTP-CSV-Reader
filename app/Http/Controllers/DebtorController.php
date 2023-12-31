<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateDebtorRequest;
use App\Models\Debtor;
use App\Models\DebtorProduct;
use App\Services\DebtorService;
use App\Services\PaginationService;
use App\Services\ValidateRequestService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
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

    public function delete(Request $request, string $debtorId, DebtorService $debtorService, ValidateRequestService $validateRequestService)
    {
        $validateRequestService->validateRequest(
            array_merge(["id" => $debtorId], ["password" => $request->get('password')]),
            ['id' => 'required', 'password' => 'required']
        );

        if (!$debtorService->deleteDebtorByIdAndPassword($debtorId, $request->get('password'))) {
            return response()->json(["error" => "Invalid credentials/request"], Response::HTTP_BAD_REQUEST);
        }

        return response()->json(["message" => "Debtor succesfully deleted"], Response::HTTP_ACCEPTED);
    }

    public function update(UpdateDebtorRequest $request)
    {
        $validated = $request->validated();

        if (!$debtor = Debtor::whereEmail($request->get('user'))) {
            return response()->json(["error" => "The account does not exists"]);
        }

        if (!Hash::check($debtor->password, $request->get('password'))) {
            return response()->json(["error" => "Invalid account credentials"]);
        }

        $debtor->update($request->toArray())->save();

        return response()->json(["message" => "Succesfully updated the users password!"]);
    }

    public function products(PaginationService $paginationService, Request $request, string $debtorId)
    {
        $pageSize = $paginationService->validatePageSize($request->input('pageSize'));

        $debtor = Debtor::where('debtor_number', '=', $debtorId)->firstOrFail();

        $products = DebtorProduct::with('product')->where('debtor_number', '=', $debtorId);

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
