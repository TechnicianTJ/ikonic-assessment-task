<?php

namespace App\Helpers;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class UserHelper
{
    public $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function createUser($data){
        $dict = $this->createUserDictionary($data,User::TYPE_MERCHANT);
        return $this->userRepository->createUser($dict);
    }

    public function createUserDictionary($data,$userType){
        $dict = [];
        $dict['name'] = $data['name'] ?? null;
        $dict['email'] = $data['email'] ?? null;
        $dict['password'] =  $data['api_key'] ?? null;
        $dict['type'] = $userType ?? null;
        return array_filter($dict);
    }

    public function getUser($email){
        return $this->userRepository->getUser($email);
    }

    public function updateUser($user,$data){
        return $this->userRepository->updateUser($user,$data);
    }
}
