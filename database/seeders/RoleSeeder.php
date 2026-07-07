<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    static function run(): void
    {
        $roles = [
            [
                'id' => 1,
                'name' => 'Admin',
                'description' => 'Administrator role with full access',
            ],
            [
                'id' => 2,
                'name' => 'Chairperson',
                'description' => 'Chairperson role with limited access',
            ],
            [
                'id' => 3,
                'name' => 'Accountant',
                'description' => 'Accountant role with financial access',
            ],
            [
                'id' => 4,
                'name' => 'Member',
                'description' => 'Member role with basic access',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['id' => $role['id']], $role);
        }
    }
}
