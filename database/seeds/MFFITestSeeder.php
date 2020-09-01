<?php

use App\Models\Product\Product;
use App\Models\Seller;
use App\Models\Shop\Shop;
use App\Models\SoldProduct\SoldProduct;
use App\Models\Transaction\Transaction;
use Illuminate\Database\Seeder;

class MFFITestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create a seller
        $seller = factory(Seller::class)->create();
        $this->command->warn("\nSeller created with id: " . $seller->user_id);

        // create a shop
        $shop = factory(Shop::class)->create([
            'name' => 'Test Shop'
        ]);
        $this->command->warn("Shop created with id: " . $shop->id);

        // create products
        $products = factory(Product::class, 5)->create();
        $product_ids = $products->map(function ($item) {
            return $item->id;
        })->toArray();
        $products_ids_text = implode(", ", $product_ids);
        $this->command->info("Products created with ids: " . $products_ids_text);

        // insert data into the pivot table (product_shop)
        $shop->products()->attach($product_ids, [
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $this->command->info("Product ids have been attached into product_shop");

        // create transactions
        $transactions = factory(Transaction::class, 8)->create([
            'shop_id' => $shop->id
        ]);
        $this->command->info("8 transactions have been created");

        // add items to the sold_products table
        $quantities = [
            [$product_ids[2] => 3, $product_ids[3] => 2, $product_ids[4] => 1],
            [$product_ids[1] => 1, $product_ids[2] => 2, $product_ids[3] => 1],
            [$product_ids[1] => 3, $product_ids[2] => 3, $product_ids[4] => 1],
            [$product_ids[0] => 3, $product_ids[2] => 5, $product_ids[3] => 3],
            [$product_ids[0] => 1, $product_ids[1] => 1, $product_ids[2] => 2, $product_ids[3] => 1],
            [$product_ids[1] => 1, $product_ids[3] => 1, $product_ids[4] => 2],
            [$product_ids[0] => 4, $product_ids[1] => 3, $product_ids[3] => 5, $product_ids[4] => 3],
            [$product_ids[1] => 1, $product_ids[2] => 2, $product_ids[3] => 1]
        ];
        $i = 0;
        foreach ($transactions as $transaction) {
            foreach ($quantities[$i] as $key => $val) {
                factory(SoldProduct::class)->create([
                    'quantity' => $val,
                    'product_id' => $key,
                    'transaction_id' => $transaction->id
                ]);
            }
            $i++;
        }
        $this->command->info("Products have been inserted into sold_products");
    }
}
