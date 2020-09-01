<?php

use Illuminate\Database\Seeder;
use App\Models\Category\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            $categoriesCount = 10;
            $subCategoriesCount = 30;

            // create some categories
            $categories = factory(Category::class, $categoriesCount)->create();
            $this->command->info("{$categoriesCount} categories have been created successfully");

            // create some sub categories under the above categories
            for ($i = 0; $i < $subCategoriesCount; $i++) {
                factory(Category::class)->create([
                    'parent_category_id' => $categories->random()->id
                ]);
            }
            $this->command->info("{$subCategoriesCount} sub categories have been created successfully");
        } catch (Exception $e) {
            $this->command->error("Some items violet the unique constraint, those are not inserted");
        }
    }
}
