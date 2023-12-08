<?php

namespace App\Services;

use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class OrderService
{
    public function __construct(
        protected AffiliateService $affiliateService
    )
    {
    }

    /**
     * Process an order and log any commissions.
     * This should create a new affiliate if the customer_email is not already associated with one.
     * This method should also ignore duplicates based on order_id.
     *
     * @param array{order_id: string, subtotal_price: float, merchant_domain: string, discount_code: string, customer_email: string, customer_name: string} $data
     * @return void
     */
    public function processOrder(array $data)
    {
        // TODO: Complete this method

        //step 0: check if the order already exist in database
        $existingOrder = $this->checkExistingOrder($data['order_id']);
        if ($existingOrder) {
            return;
        }

        //step 1: query affiliate with discount code if not found create new
        $affiliate = $this->queryAffiliate($data['discount_code']);

        if ($affiliate) {
            //step 2: register affiliate i.e
            $this->affiliateService->register($affiliate->merchant, $data['customer_email'], $data['customer_name'], 0.1);
        } else {
            // NO TEST CASE FOR THIS SCENARIO

            // POSSIBLE SOLUTION

            //create user
            $user = $this->createUser($data['customer_email'],$data['name']);

            //create merchant
            $merchant = $this->createMerchant($user,$data['merchant_domain']);

            //create affiliate
            $affiliate = $this->createAffiliate($user,$merchant,$data['discount_code']);

            //register affiliate
            $this->affiliateService->register($affiliate->merchant, $data['customer_email'], $data['customer_name'], 0.1);
        }

        //procecss order
        $this->createOrder($data['subtotal_price'],$data['order_id'],$affiliate);
    }

    public function createOrder($subtotalPrice,$orderId,$affiliate)
    {
        $order = new Order([
            'subtotal' => $subtotalPrice,
            'external_order_id' => $orderId,
            'merchant_id' => $affiliate->merchant->id,
            'affiliate_id' => $affiliate->id,
        ]);

        $order->commission_owed = $subtotalPrice * $affiliate->commission_rate;
        $order->save();
    }

    public function createAffiliate($user,$merchant,$discountCode)
    {
        return Affiliate::create([
            'user_id' => $user->id,
            'merchant_id' => $merchant->id,
            'discount_code' => $discountCode,
        ]);
    }

    public function createMerchant($user,$domain)
    {
        return Merchant::create([
            'user_id' => $user->id,
            'domain_email' => $domain,
        ]);
    }

    public function createUser($email,$name)
    {
        return User::create([
            'email' => $email,
            'name' => $name,
        ]);
    }

    public function queryAffiliate($discount_code){
        return $affiliate = Affiliate::where(['discount_code' => $discount_code])->first();
    }

    public function checkExistingOrder($orderId): bool
    {
        return Order::where('external_order_id', $orderId)->exists();
    }


}
