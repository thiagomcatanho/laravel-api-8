<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        foreach (range(1, 10) as $i) {
            DB::table('customers')->insert([
                'name' => $faker->name,
                'email' => $faker->email,
                'utr' => $faker->randomNumber(),
                'dob'=> $faker->dateTimeBetween('-80 years', '-18 years'),
                'phone' => $faker->phoneNumber,
                'profile_pic_path' => $faker->image()
            ]);
        }
    }
}
