<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::truncate();
        User::create([
            'id' => 1,
            'name' => 'John',
            'surname' => 'Doe',
            'email' => 'testemail@example.com',
            'password' => Hash::make('testpassword1234'),
            'email_verified_at' => now()
        ]);
    }
}
