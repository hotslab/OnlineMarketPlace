<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'image' => $this->faker->name(),
            'name' => $this->faker->name(),
            'price' => $this->getImage()
        ];
    }

    protected function getImage()
    {
        $images = [
            'testimages/beanie.jpeg',
            'testimages/dress.jpeg',
            'testimages/greyfridge.png',
            'testimages/greystove.jpeg',
            'testimages/silverfridge.png',
            'testimages/silverstove.jpeg',
            'testimages/sneaker.jpeg'
        ];
        shuffle($images);
        $key = array_rand($images, 1);
        return $images[$key];
    }
}
