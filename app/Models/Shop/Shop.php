<?php

namespace App\Models\Shop;

use App\Models\Seller;
use App\Models\Employee;
use App\AbstractAPIModel;
use App\Models\Product\Product;
use App\Models\Transaction\Transaction;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shop extends AbstractAPIModel
{
    use SoftDeletes;

    protected $fillable = ['name'];

    /**
     * override the model type
     */
    public function type()
    {
        return 'shops';
    }

    /**
     * shop belongs to many users who are sellers
     * 
     * @return Seller collection
     */
    public function sellers()
    {
        return $this->belongsToMany(Seller::class)->withTimestamps();
    }

    /**
     * shop has many employees
     * 
     * @return Employee collection
     */
    public function employees()
    {
        return $this->belongsToMany(Employee::class)->withTimestamps();
    }

    /**
     * shop has many products
     * 
     * @return Product collection
     */
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    /**
     * shop has many shop types
     * 
     * @return ShopType collection
     */
    public function shopTypes()
    {
        return $this->belongsToMany(ShopType::class);
    }

    /**
     * shop has many transactions
     * 
     * @return Transaction collection
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * shop has many stocks
     */
    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }
}
