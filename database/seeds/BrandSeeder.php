<?php

use App\Models\Brand\Brand;
use Illuminate\Database\Seeder;
use App\Services\Utilities\UniqueItemSeeding;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $brandsCount = 30;

        // create items with unique check
        (new UniqueItemSeeding($this->command))
            ->seedWithUniqueConstraint(Brand::class, $brandsCount);
    }
}
