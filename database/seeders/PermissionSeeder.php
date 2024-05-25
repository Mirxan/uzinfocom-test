<?php

namespace Database\Seeders;

use App\Models\Permission\PermissionRole;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            2 => [1, 2, 3],
            3 => [1, 2, 3, 4, 5],
        ];

        foreach ($permissions as $role_id => $permission) {
            foreach ($permission as $p) {
                PermissionRole::create([
                    'role_id' => $role_id,
                    'permission_id' => $p,
                ]);
            }
        }
    }
}
