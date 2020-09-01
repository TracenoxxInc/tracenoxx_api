<?php

namespace App\Models\Category;

use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'parent_category_id'];

    public $timestamps = false;

    /**
     * all subcategories have parent category except the parent categories
     */
    public function parent_category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * a category has many products
     * 
     * @return Product collection
     */
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
