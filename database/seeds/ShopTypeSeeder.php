<?php

use App\Models\Shop\ShopType;
use Illuminate\Database\Seeder;
use App\Services\Utilities\UniqueItemSeeding;

class ShopTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $shopTypesCount = 20;

        // create items with unique check
        (new UniqueItemSeeding($this->command))
            ->seedWithUniqueConstraint(ShopType::class, $shopTypesCount);
    }
}
