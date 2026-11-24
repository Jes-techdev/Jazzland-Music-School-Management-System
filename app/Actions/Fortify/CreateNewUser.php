<?php

namespace App\Actions\Fortify;

use App\Models\Guardian;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    public function create(array $input): User
    {
        Validator::make($input, [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::default(),
            ],
        ])->validate();

        return DB::transaction(function () use ($input) {

            $parentRole = Role::where('name', 'parent')->firstOrFail();

            $fullName = trim(
                $input['first_name'] . ' ' . $input['last_name']
            );

            $user = User::create([
                'name' => $fullName,
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
                'role_id' => $parentRole->id,
            ]);

            Guardian::create([
                'user_id' => $user->id,
                'first_name' => $input['first_name'],
                'last_name' => $input['last_name'],
                'email' => $input['email'],
                'phone' => $input['phone'],
                'address' => $input['address'] ?? null,
            ]);

            return $user;
        });
    }
}
