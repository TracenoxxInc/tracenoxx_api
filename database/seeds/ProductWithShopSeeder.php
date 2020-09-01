<?php

use App\Models\Product\Product;
use App\Models\Shop\Shop;
use Illuminate\Database\Seeder;

class ProductWithShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            $productsCount = 100;
            $shopsCount = 10;

            // create some shops
            $shops = factory(Shop::class, $shopsCount)->create();
            $this->command->info("{$shopsCount} shops have been created successfully");

            // create some products
            $products = factory(Product::class, $productsCount)->create();
            $this->command->info("{$productsCount} products have been created successfully");

            // sync the products with shop
            $products->each(function ($product, $key) use ($shops) {
                $product->shops()->sync($shops->random(rand(1, 3))->pluck('id'));
            });
        } catch (Exception $e) {
            $this->command->error("Some items violet the unique constraint, those are skiped");
        }
    }
}
