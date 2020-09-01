<?php

use App\Models\Shop\Shop;
use Illuminate\Database\Seeder;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $shopsCount = 10;

        factory(Shop::class, $shopsCount)->create();
        $this->command->info("{$shopsCount} shops have been created successfully");
    }
}
