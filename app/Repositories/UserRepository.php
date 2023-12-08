<?php


namespace App\Repositories;


use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    public function createUser($data)
    {
        return User::create($data);
    }

    public function getUser($email)
    {
        return User::where('email', $email)->first();
    }

    public function updateUser($user, $data)
    {
        return $user->update([
            'email' => $data['email'],
            'password' => $data['api_key']
        ]);
    }

}
