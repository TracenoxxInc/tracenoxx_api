<?php

namespace App\Models\Shop;

use App\AbstractAPIModel;

class ShopType extends AbstractAPIModel
{
    protected $fillable = ['name', 'description', 'image'];

    /**
     * override the model type
     */
    public function type()
    {
        return 'shop-types';
    }

    /**
     * shop type belongs to many shops
     * 
     * @return Shop
     */
    public function shops()
    {
        return $this->belongsToMany(Shop::class);
    }
}
