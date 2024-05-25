<?php

namespace Database\Seeders;

use App\Models\Role\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    const PASSWORD = "secret";
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        $roles = Role::all();

        foreach ($roles as $role) {
            $user = User::create([
                'name' => 'My Name is ' . $role->name,
                'email' => strtolower($role->name) . '@example.com',
                "password" => self::PASSWORD,
            ]);
            $user->roles()->attach($role->id);
        }
    }
}
