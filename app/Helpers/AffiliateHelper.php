<?php


namespace App\Helpers;


use App\Jobs\PayoutOrderJob;
use App\Models\Order;
use App\Repositories\AffiliateRepository;

class AffiliateHelper
{
    public $affiliateRepo;

    public function __construct(){
        $this->affiliateRepo = new AffiliateRepository();
    }

    public function checkIfEmailIsUnique($merchant,$email){
        $uniqueEmail = true;

        //email is associated with some other merchant
        if($merchant->user->email === $email){
            $uniqueEmail = false;
        }

        //email is associated with some other affiliate
        if($this->affiliateRepo->getAffiliate($email)){
            $uniqueEmail = false;
        }

        return $uniqueEmail;
    }

    public function createAffiliate($data){
        return $this->affiliateRepo->createAffiliate($data);
    }

    public function createAffiliateDictionary($userId,$merchantId,$commissionRate,$discountCode){
        $dict = [];

        $dict['merchant_id'] = $merchantId ?? null;
        $dict['user_id'] = $userId ?? null;
        $dict['commission_rate'] = $commissionRate ?? null;
        $dict['discount_code'] = $discountCode ?? null;

        return array_filter($dict);
    }

    public function payoutUnpaidOrders($affiliate){
        $unpaidOrders = $this->affiliateRepo->getUnpaidOrders($affiliate);

        // Dispatch the payout job for each unpaid order
        foreach ($unpaidOrders as $order) {
            PayoutOrderJob::dispatch($order);
        }
    }

}
