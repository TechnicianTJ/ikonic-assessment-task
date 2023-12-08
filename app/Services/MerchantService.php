<?php

namespace App\Services;

use App\Helpers\AffiliateHelper;
use App\Helpers\MerchantHelper;
use App\Helpers\UserHelper;
use App\Jobs\PayoutOrderJob;
use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;

class MerchantService
{
    public $userHelper;
    public $merchantHelper;

    public function __construct(){
        $this->userHelper = new UserHelper();
        $this->merchantHelper = new MerchantHelper();
    }

    /**
     * Register a new user and associated merchant.
     * Hint: Use the password field to store the API key.
     * Hint: Be sure to set the correct user type according to the constants in the User model.
     *
     * @param array{domain: string, name: string, email: string, api_key: string} $data
     * @return Merchant
     */
    public function register(array $data): Merchant
    {
        // TODO: Complete this method
            //step 1: create user
            $user = $this->userHelper->createUser($data);
            $data['userId'] = $user->id;

            //step 2: create merchant
            return $this->merchantHelper->createMerchant($data);
    }

    /**
     * Update the user
     *
     * @param array{domain: string, name: string, email: string, api_key: string} $data
     * @return void
     */
    public function updateMerchant(User $user, array $data)
    {
        // TODO: Complete this method
        try{
            //update merchant with
            $this->merchantHelper->updateMerchant($user->merchant,$data);

            //update user
            $this->userHelper->updateUser($user,$data);
        }catch (\Exception $ex){
            return $ex;
        }
    }

    /**
     * Find a merchant by their email.
     * Hint: You'll need to look up the user first.
     *
     * @param string $email
     * @return Merchant|null
     */
    public function findMerchantByEmail(string $email): ?Merchant
    {
        // TODO: Complete this method
        try{
            $user = $this->userHelper->getUser($email);
            return $user ? $user->merchant : null;
        }catch (\Exception $ex){
            return $ex;
        }
    }

    /**
     * Pay out all of an affiliate's orders.
     * Hint: You'll need to dispatch the job for each unpaid order.
     *
     * @param Affiliate $affiliate
     * @return void
     */
    public function payout(Affiliate $affiliate)
    {
        // TODO: Complete this method
        try{
            return (new AffiliateHelper())->payoutUnpaidOrders($affiliate);
        }catch (\Exception $ex){
            return $ex;
        }
    }
}
