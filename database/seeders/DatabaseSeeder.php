<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::firstOrCreate(['name' => 'registrar']);
        Role::firstOrCreate(['name' => 'bursar']);
        Role::firstOrCreate(['name' => 'lecturer']);
        Role::firstOrCreate(['name' => 'it_admin']);

        $superuser = User::factory()->create([
            'name'     => 'IT Admin',
            'email'    => 'it@example.com',
            'password' => 'password',
        ]);
        $superuser->assignRole('it_admin');

        $registrar = User::factory()->create([
            'name'     => 'Registrar',
            'email'    => 'registrar@example.com',
            'password' => 'password',
        ]);
        $registrar->assignRole('registrar');

        $bursar = User::factory()->create([
            'name'     => 'Bursar',
            'email'    => 'bursar@example.com',
            'password' => 'password',
        ]);
        $bursar->assignRole('bursar');

        $lecturer = User::factory()->create([
            'name'     => 'Lecturer',
            'email'    => 'lecturer@example.com',
            'password' => 'password',
        ]);
        $lecturer->assignRole('lecturer');
    }
}