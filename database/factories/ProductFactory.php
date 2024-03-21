<?php

namespace Database\Factories;

use Illuminate\Support\Str;
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

        $title = $this->faker->name();
        $slug  = Str::slug($title);

        $sub_cat = [14,15,16,17];
        $sub_cat_rand_key = array_rand($sub_cat);

        $brands = [6,7,8,9];
        $brands_key = array_rand($brands);

        return [
            'title' => $title,
            'slug' =>$slug,
            'description' =>'This is test Description',
            'category_id' => 52, //rand(50,51,52),
            'sub_category_id' => $sub_cat[$sub_cat_rand_key],
            'brand_id' => $brands[$brands_key],
            'price' => rand(10,1000),
            'compare_price' => rand(500,1000),
            'sku' => rand(1000,10000),
            'track_qty' => 'Yes',
            'qty' => rand(1,20),
            'is_featured' => 'Yes',
            'status' => 1,
         ];
    }
}
