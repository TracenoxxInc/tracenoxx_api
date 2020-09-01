<?php

namespace App\Models;

use App\User;
use App\Models\Shop\Shop;
use App\Models\Transaction\Transaction;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = ['user_id', 'is_active', 'manager_id'];

    /**
     * staff is also a user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * staff has a manager
     */
    public function manager()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * employees belongs to many shops
     * 
     * @return Shop collection
     */
    public function shops()
    {
        return $this->belongsToMany(Shop::class)->withTimestamps();
    }

    /**
     * employee sells many transactions
     * 
     * @return Transaction collection
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
