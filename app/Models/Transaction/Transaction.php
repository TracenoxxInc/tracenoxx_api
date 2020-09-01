<?php

namespace App\Models\Transaction;

use App\Models\Employee;
use App\Models\Shop\Shop;
use App\Models\SoldProduct\SoldProduct;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['user_id', 'shop_id', 'employee_id'];

    /**
     * transaction belong to a user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * transaction belong to a shop
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * transaction belong to a employee
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * transaction has many sold products
     */
    public function sold_products()
    {
        return $this->hasMany(SoldProduct::class);
    }
}
