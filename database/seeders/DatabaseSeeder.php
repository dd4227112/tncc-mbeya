<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\PermissionSeeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        RoleSeeder::run();

        $user = User::factory()->create([
            'first_name' => 'Kasumulu',
            'last_name' => 'TNCC',
            'email' => 'testadmin@tncckasumulu.or.tz',
            'password' => bcrypt('password'),
            'phone' => '+255743123456',
        ]);
        $user->roles()->attach(1); // Assign Admin role to the user
        $this->unitSeeder();
        PermissionSeeder::run();
        $this->seedAdminPermission();
    }
    public function unitSeeder(): void
    {
        $units = [
            ['name' => 'Kilogram', 'abbreviation' => 'Kg'],
            ['name' => 'Bag', 'abbreviation' => 'Bag'],
            ['name' => 'Piece', 'abbreviation' => 'pc'],
        ];
        foreach ($units as $unit) {
            Unit::create($unit);
        }
    }
    public function seedAdminPermission()
    {
        $role = Role::where('name', 'ilike', 'admin')->first();
        if (!empty($role)) {
            $permissions = Permission::pluck('id');
            $role->permissions()->sync($permissions);
        }
    }
}
