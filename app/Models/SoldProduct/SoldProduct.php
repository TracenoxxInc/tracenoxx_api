<?php

namespace App\Models\SoldProduct;

use Illuminate\Database\Eloquent\Model;

class SoldProduct extends Model
{
    protected $fillable = [
        'quantity',
        'list_price',
        'discount',
        'product_id',
        'transaction_id'
    ];

    /**
     * sold product belong to products
     */
    public function product()
    {
        return $this->belongsTo('App\Models\Product\Product');
    }

    /**
     * sold product belong to transactions
     */
    public function transaction()
    {
        return $this->belongsTo('App\Models\Transaction\Transaction');
    }
}
