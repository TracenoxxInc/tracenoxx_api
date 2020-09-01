<?php

use App\Models\Seller;
use Illuminate\Database\Seeder;

class SellerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $SellersCount = 20;

        factory(Seller::class, $SellersCount)->create();
        $this->command->info("{$SellersCount} sellers have been created successfully");
    }
}
