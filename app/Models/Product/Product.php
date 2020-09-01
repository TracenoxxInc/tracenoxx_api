<?php

namespace App\Models\Product;

use App\Models\Shop\Shop;
use App\Models\Brand\Brand;
use App\Models\Category\Category;
use App\Models\SoldProduct\SoldProduct;
use App\Models\Stock\Stock;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'model_number',
        'product_unit_id',
        'brand_id'
    ];

    /**
     * product belongs to many shops
     * 
     * @return Shop collection
     */
    public function shops()
    {
        return $this->belongsToMany(Shop::class);
    }

    /**
     * product has a brand
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * product has a unit
     */
    public function product_unit()
    {
        return $this->belongsTo(ProductUnit::class);
    }

    /**
     * product belongs to many categories
     * 
     * @return Category collection
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * transaction has many sold products
     */
    public function sold_products()
    {
        return $this->hasMany(SoldProduct::class);
    }

    /**
     * product has many stocks
     */
    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }
}
