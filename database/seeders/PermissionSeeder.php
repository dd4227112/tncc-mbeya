<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    static function run(): void
    {
        $resources = [
            'units' => ['view', 'create', 'update', 'delete'],
            'users' => ['view', 'create', 'update', 'delete'],
            'crops' => ['view', 'create', 'update', 'delete'],
            'invoices' => ['view', 'create', 'update', 'delete', 'print'],
            'payments' => ['view', 'create', 'update', 'delete', 'print'],
            'settings' => ['view', 'update'],
            'reports' => ['view'],
        ];

        foreach ($resources as $resource => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name' => "{$resource}.{$action}",
                    'guard_name' => 'web',
                ]);
            }
        }
    }
}
