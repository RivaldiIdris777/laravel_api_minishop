<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

         // ===== STEP 1: Buat Permissions =====
        Permission::create(['name' => 'create products']);
        Permission::create(['name' => 'read products']);
        Permission::create(['name' => 'update products']);
        Permission::create(['name' => 'delete products']);

        Permission::create(['name' => 'create orders']);
        Permission::create(['name' => 'read orders']);
        Permission::create(['name' => 'update orders']);
        Permission::create(['name' => 'delete orders']);

        Permission::create(['name' => 'create users']);
        Permission::create(['name' => 'read users']);
        Permission::create(['name' => 'update users']);
        Permission::create(['name' => 'delete users']);

        Permission::create(['name' => 'manage roles']);
        Permission::create(['name' => 'manage permissions']);

         // ===== STEP 2: Buat Roles =====
        $adminRole = Role::create(['name' => 'admin']);
        $editorRole = Role::create(['name' => 'editor']);
        $userRole = Role::create(['name' => 'user']);
        $customerRole = Role::create(['name' => 'customer']);

        // ===== STEP 3: Assign Permissions ke Roles =====
        // Admin: punya semua permissions
        $adminRole->syncPermissions(Permission::all());

         // Editor: manage products dan orders
        $editorRole->syncPermissions([
            'create products', 'read products', 'update products', 'delete products',
            'create orders', 'read orders', 'update orders'
        ]);

        // User: read only
        $userRole->syncPermissions([
            'read products', 'read orders', 'read users'
        ]);

        // Customer: bisa baca products dan buat orders
        $customerRole->syncPermissions([
            'read products', 'create orders', 'read orders'
        ]);

    }
}
