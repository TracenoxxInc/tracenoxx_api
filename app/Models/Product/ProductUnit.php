<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class ProductUnit extends Model
{
    protected $fillable = ['name', 'multiplier'];

    public $timestamps = false;

    /**
     * override the model type
     */
    public function type()
    {
        return 'product-units';
    }

    /**
     * return the allowed attributes of a model
     * 
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function allowedAttributes()
    {
        return collect($this->attributes)->filter(function ($item, $key) {
            return !collect($this->hidden)->contains($key) && $key !== 'id';
        });
    }


    /**
     * many products have the same unit 
     * 
     * @return Product collection
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
