<?php

namespace App\Models\LinguisticTerms;

use Illuminate\Database\Eloquent\Model;

class LinguisticSums extends Model
{
    protected $fillable = ['product_id', 'version_id', 'f_value', 'term'];
}
