<?php

namespace App\Models;

use App\AbstractAPIModel;
use App\Models\Shop\Shop;
use App\User;
use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class Seller extends AbstractAPIModel
{
    use SoftDeletes, SoftCascadeTrait;

    /**
     * override the model type
     */
    public function type()
    {
        return 'sellers';
    }

    /**
     * cascade soft delete relations
     */
    protected $softCascade = ['shops'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id'
    ];

    /**
     * seller is also a user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * override with plural form, because of the JSON API specifications
     */
    public function users()
    {
        return $this->user();
    }

    /**
     * user can create many shops
     */
    public function shops()
    {
        return $this->belongsToMany(Shop::class)->withTimestamps();
    }
}
