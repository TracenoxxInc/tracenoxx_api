<?php

namespace App\Models\LinguisticTerms;

use Illuminate\Database\Eloquent\Model;

class TriangleLinguistic extends Model
{
    protected $fillable = ['version_id', 'product_id', 'transaction_id', 'low', 'middle', 'high'];
}
