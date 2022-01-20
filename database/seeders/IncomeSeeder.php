<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class IncomeSeeder extends Seeder
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
            DB::table('incomes')->insert([
                'customer_id' => Customer::inRandomOrder()->first()->id,
                'description' => $faker->company(),
                'amount' => $faker->randomFloat(2, 0, 99999),
                'income_date' => $faker->dateTime(),
                'tax_year' => $faker->year(),
                'income_file_path' => $faker->image()
            ]);
        }
    }
}
