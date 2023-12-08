<?php


namespace App\Helpers;


use App\Models\Order;
use App\Repositories\MerchantRepository;
use Illuminate\Support\Carbon;

class MerchantHelper
{
    public $merchantRepository;

    public function __construct()
    {
        $this->merchantRepository = new MerchantRepository();
    }

    public function createMerchant($data){
        $dict = $this->createMerchantDictionary($data);
        return $this->merchantRepository->createMerchant($dict);
    }

    public function createMerchantDictionary($data){
        $dict = [];
        $dict['user_id'] = $data['userId'] ?? null;
        $dict['domain'] = $data['domain'] ?? null;
        $dict['display_name'] = $data['name'] ?? null;
        return array_filter($dict);
    }

    public function updateMerchant($merchant,$data){
        return $this->merchantRepository->updateMerchant($merchant,$data);
    }

    public function getUserMerchant($userId){
        return $this->merchantRepository->getUserMerchant($userId);
    }

    public function getOrderStats($inputs){
        $from = Carbon::parse($inputs['from']);
        $to = Carbon::parse($inputs['to']);

        $orders = ((new OrderHelper())->getOrderWithInRange($from,$to));

        $count = $orders->count();
        $allCommission = $orders->sum('commission_owed');
        $noAffiliate = $orders->whereNull('affiliate_id')->sum('commission_owed');
        $revenu = $orders->sum('subtotal');

        $commissionsOwed = $allCommission - $noAffiliate;

        return response()->json([
            'count' => $count,
            'commissions_owed' => $commissionsOwed,
            'revenue' => $revenu
        ]);
    }
}
