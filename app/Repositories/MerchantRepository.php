<?php


namespace App\Repositories;


use App\Models\Merchant;
use App\Models\Order;

class MerchantRepository
{
    public function createMerchant($data)
    {
//        dd($data);
        return Merchant::create($data);
    }

    public function updateMerchant($merchant, $data)
    {
        return $merchant->update([
            'domain' => $data['domain'],
            'display_name' => $data['name']
        ]);
    }

    public function getUserMerchant($userId)
    {
        return Merchant::where('user_id', $userId)->get();
    }

}
