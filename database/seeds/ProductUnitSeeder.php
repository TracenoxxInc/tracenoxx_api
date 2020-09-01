<?php

use App\Models\Product\ProductUnit;
use App\Services\Utilities\UniqueItemSeeding;
use Illuminate\Database\Seeder;

class ProductUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $productUnitsCount = 20;

        // create items with unique check
        (new UniqueItemSeeding($this->command))
            ->seedWithUniqueConstraint(ProductUnit::class, $productUnitsCount);
    }
}
