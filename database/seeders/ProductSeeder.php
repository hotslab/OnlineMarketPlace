<?php

namespace Database\Seeders;
use App\Models\Product;

use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::truncate();
        Product::insert([
            [
                'id' => 1,
                'image' => 'testimages/beanie.jpeg',
                'name' => 'Beanie',
                'price'=> 7.99,
            ],
            [
                'id' => 2,
                'image' => 'testimages/dress.jpeg',
                'name' => 'Dress',
                'price'=> 23.99,
            ],
            [
                'id' => 3,
                'image' => 'testimages/greyfridge.png',
                'name' => 'Grey Fridge',
                'price'=> 220.40,
            ],
            [
                'id' => 4,
                'image' => 'testimages/greystove.jpeg',
                'name' => 'Grey Stove',
                'price'=> 201.56,
            ],
            [
                'id' => 5,
                'image' => 'testimages/silverfridge.png',
                'name' => 'Silver Fridge',
                'price'=> 311.99,
            ],
            [
                'id' => 6,
                'image' => 'testimages/silverstove.jpeg',
                'name' => 'Silver Stove',
                'price'=> 223.83,
            ],
            [
                'id' => 7,
                'image' => 'testimages/sneaker.jpeg',
                'name' => 'Sneaker',
                'price'=> 35.89,
            ]
        ]);

    }
}
