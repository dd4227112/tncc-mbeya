<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;

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
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'phone' => '+255743123456',
        ]);
        $user->roles()->attach(1); // Assign Admin role to the user
        $this->unitSeeder();
    }
    public function unitSeeder(): void
    {
        $units = [
            ['name' => 'Kilogram', 'abbreviation' => 'Kg'],
            ['name' => 'Liter', 'abbreviation' => 'L'],
            ['name' => 'Piece', 'abbreviation' => 'pc'],
        ];
        foreach ($units as $unit) {
            Unit::create($unit);
        }
    }
}
