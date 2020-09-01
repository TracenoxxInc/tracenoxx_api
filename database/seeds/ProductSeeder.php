<?php

use App\Models\Product\Product;
use Illuminate\Database\Seeder;
use App\Services\Utilities\UniqueItemSeeding;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $productsCount = 100;

        // create some product units
        (new UniqueItemSeeding($this->command))
            ->seedWithUniqueConstraint(Product::class, $productsCount);
    }
}
