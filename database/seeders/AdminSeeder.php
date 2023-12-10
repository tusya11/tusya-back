<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        $faker = Factory::create();
        $admins = ['admin@mail.ru', 'admin2@mail.ru'];

        foreach ($admins as $admin) {
            $user = User::create([
                'email' => $admin,
                'password' => Hash::make('123456789'),
                'role_id' => 3,
            ]);

            $profile = new Profile([
                "first_name" => $faker->firstName(),
                "middle_name" => null,
                "second_name" => $faker->lastName(),
                "phone" => "+71113241223",
                "gender" => "male",
            ]);

            $user->profile()->save($profile);

            $user->update([
                'profile_id' => $profile->id,
            ]);
        }
    }
}