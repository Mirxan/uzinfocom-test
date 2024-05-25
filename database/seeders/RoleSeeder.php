<?php

namespace Database\Seeders;

use App\Models\Role\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                "name" => "Admin",
            ],
            [
                "name" => "User",
            ],
            [
                "name" => "Moderator",
            ]
        ];
        Role::insert($roles);
    }
}
