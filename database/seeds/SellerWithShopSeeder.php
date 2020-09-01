<?php

use App\Models\Seller;
use App\Models\Shop\Shop;
use Illuminate\Database\Seeder;

class SellerWithShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sellersCount = 10;
        $shopsCount = 10;

        // create some shops
        $shops = factory(Shop::class, $shopsCount)->create();
        $this->command->info("{$shopsCount} shops have been created successfully");

        // create some sellers
        $sellers = factory(Seller::class, $sellersCount)->create();
        $this->command->info("{$sellersCount} sellers have been created successfully");

        // sync the sellers with shop
        $shops->each(function ($shop, $key) use ($sellers) {
            $shop->sellers()->sync($sellers->random(rand(1, 3))->pluck('id'));
        });
    }
}
