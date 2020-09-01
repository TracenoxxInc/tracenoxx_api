<?php

namespace App\Models\Stock;

use App\Models\Shop\Shop;
use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = ['shop_id', 'product_id', 'quantity'];

    /**
     * stock has a product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * stock belong to a shop
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
