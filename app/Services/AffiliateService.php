<?php

namespace App\Services;

use App\Exceptions\AffiliateCreateException;
use App\Helpers\AffiliateHelper;
use App\Helpers\UserHelper;
use App\Mail\AffiliateCreated;
use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class AffiliateService
{
    public function __construct(
        protected ApiService $apiService
    )
    {
    }

    /**
     * Create a new affiliate for the merchant with the given commission rate.
     *
     * @param Merchant $merchant
     * @param string $email
     * @param string $name
     * @param float $commissionRate
     * @return Affiliate
     */
    public function register(Merchant $merchant, string $email, string $name, float $commissionRate): Affiliate
    {
        // TODO: Complete this method
        //stp 1: check if the given email is unique or not
        $uniquEmail = (new AffiliateHelper())->checkIfEmailIsUnique($merchant, $email);
        if (!$uniquEmail) {
            throw new AffiliateCreateException();
        } else {
            // create user of type affiliate
            $user = (new UserHelper())->createUser(['email' => $email, 'name' => $name, User::TYPE_AFFILIATE]);

            // associate new discount code from merchant and create a new affiliate with newly create user
            $affiliateHelper = (new AffiliateHelper());
            $discountCode = $this->apiService->createDiscountCode($merchant)['code'];
            $affiliateDict = $affiliateHelper->createAffiliateDictionary($user->id, $merchant->id, $commissionRate, $discountCode);
            $affiliate = $affiliateHelper->createAffiliate($affiliateDict);

            // Send an email notification
            Mail::to($user->email)->send(new AffiliateCreated($affiliate));
            return $affiliate;
        }
    }
}
