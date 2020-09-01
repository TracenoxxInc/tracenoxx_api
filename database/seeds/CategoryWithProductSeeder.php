<?php

use Illuminate\Database\Seeder;
use App\Models\Category\Category;
use App\Models\Product\Product;

class CategoryWithProductSeeder extends Seeder
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
            $productsCount = 50;

            // create some categories
            $categories = factory(Category::class, $categoriesCount)->create();
            $this->command->info("{$categoriesCount} categories have been created successfully");

            // create some sub categories under the above categories
            $categoriesCollection = $categories;
            for ($i = 0; $i < $subCategoriesCount; $i++) {
                $category = factory(Category::class)->create([
                    'parent_category_id' => $categories->random()->id
                ]);
                $categoriesCollection->push($category);
            }
            $this->command->info("{$subCategoriesCount} subcategories have been created successfully");

            // create some products 
            $products = factory(Product::class, $productsCount)->create();

            // sync the many to many relationship
            $products->each(function ($product, $key) use ($categoriesCollection) {
                $product->categories()->sync($categoriesCollection->random(rand(1, 3))->pluck('id'));
            });
        } catch (Exception $e) {
            $this->command->error("Some items violet the unique constraint, those are skiped");
        }
    }
}
