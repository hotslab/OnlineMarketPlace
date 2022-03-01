<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserProduct;

class UserProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UserProduct::truncate();
        UserProduct::insert([
            [ 'product_id' => 1, 'user_id' => 1 ],
            [ 'product_id' => 2, 'user_id' => 1 ],
            [ 'product_id' => 3, 'user_id' => 1 ],
            [ 'product_id' => 4, 'user_id' => 1 ],
            [ 'product_id' => 5, 'user_id' => 1 ],
            [ 'product_id' => 6, 'user_id' => 1 ],
            [ 'product_id' => 7, 'user_id' => 1 ]
        ]);
    }
}
