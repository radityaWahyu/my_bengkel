<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        print("Mulai membuat user administrator \n");
        $employee = Employee::create([
            'name' => 'Administrator',
            'gender' => 'l',
            'phone' => '1234',
            'whatsapp' => '1234',
            'address' => 'SMKN 1 Purwosari',
        ]);

        $user = $employee->user()->create([
            'username' => 'administrator',
            'password' => '12345678',
            'is_enabled' => true,
        ]);


        Role::create(['name' => 'administrator']);
        Role::create(['name' => 'operator']);
        Role::create(['name' => 'mekanik']);
        //$permissions = Permission::where('name', 'manage-jurusan')->first();
        $user->assignRole('administrator');
        print("Pembuatan user administrator selesai \n");
    }
}
