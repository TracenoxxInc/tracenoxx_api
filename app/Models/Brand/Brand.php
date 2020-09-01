<?php

namespace App\Models\Brand;

use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = ['name'];

    public $timestamps = false;

    /**
     * override the model type
     */
    public function type()
    {
        return 'brands';
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
     * brand has many products
     * 
     * @return Product collection
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
