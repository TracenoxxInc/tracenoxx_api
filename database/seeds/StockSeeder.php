<?php

use App\Models\Stock\Stock;
use Illuminate\Database\Seeder;
use App\Services\Utilities\UniqueItemSeeding;

class StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $stocksCount = 200;

        // create items with unique check
        (new UniqueItemSeeding($this->command))
            ->seedWithUniqueConstraint(Stock::class, $stocksCount);
    }
}
