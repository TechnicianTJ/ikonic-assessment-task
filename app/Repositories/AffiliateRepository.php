<?php


namespace App\Repositories;


use App\Models\Affiliate;
use App\Models\Order;
use App\Models\User;

class AffiliateRepository
{
    public function getAffiliate($email){
        return Affiliate::whereHas('user',function ($user) use ($email) {
            $user->where('email',$email);
        })->first();
    }


    public function createAffiliate($data){
        return Affiliate::create($data);
    }

    public function getUnpaidOrders($affiliate){
        return $affiliate->orders()->where('payout_status', Order::STATUS_UNPAID)->get();
    }

    public function checkEmail($email){
        return User::where('email',$email)->has('affiliate')->first();
    }
}
