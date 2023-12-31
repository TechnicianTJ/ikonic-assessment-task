<?php

namespace App\Http\Controllers;

use App\Helpers\MerchantHelper;
use App\Services\MerchantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MerchantController extends Controller
{
    public function __construct(
        MerchantService $merchantService
    )
    {
    }

    /**
     * Useful order statistics for the merchant API.
     *
     * @param Request $request Will include a from and to date
     * @return JsonResponse Should be in the form {count: total number of orders in range, commission_owed: amount of unpaid commissions for orders with an affiliate, revenue: sum order subtotals}
     */
    public function orderStats(Request $request): JsonResponse
    {
        // TODO: Complete this method
        try{
            $inputs = $request->all();
            return (new MerchantHelper())->getOrderStats($inputs);
        }catch (\Exception $ex){
            throw $ex;
        }
    }
}
