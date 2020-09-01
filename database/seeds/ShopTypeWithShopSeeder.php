<?php

use App\Models\Shop\Shop;
use App\Models\Shop\ShopType;
use Illuminate\Database\Seeder;

class ShopTypeWithShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $shopTypesCount = 20;
        $shopsCount = 10;

        // create some shops
        $shops = factory(Shop::class, $shopsCount)->create();
        $this->command->info("{$shopsCount} shops have been created successfully");

        // create some types
        $shopTypes = factory(ShopType::class, $shopTypesCount)->create();
        $this->command->info("{$shopTypesCount} shop types have been created successfully");

        // sync the shop types with shop
        $shopTypes->each(function ($type, $key) use ($shops) {
            $type->shops()->sync($shops->random(rand(1, 3))->pluck('id'));
        });
    }
}
