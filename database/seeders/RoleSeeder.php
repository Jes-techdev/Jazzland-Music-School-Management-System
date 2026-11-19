<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'manager',
                'description' => 'Full system management access',
            ],
            [
                'name' => 'instructor',
                'description' => 'Instructor portal access',
            ],
            [
                'name' => 'parent',
                'description' => 'Parent and guardian portal access',
            ],
            [
                'name' => 'student',
                'description' => 'Adult student portal access',
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name']],
                ['description' => $role['description']]
            );
        }
    }
}
