<?php

use Illuminate\Database\Seeder;
use App\Models\SoldProduct\SoldProduct;
use App\Services\Utilities\UniqueItemSeeding;

class SoldProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $soldProductsCount = 1000;

        // create items with unique check
        (new UniqueItemSeeding($this->command))
            ->seedWithUniqueConstraint(SoldProduct::class, $soldProductsCount);
    }
}
