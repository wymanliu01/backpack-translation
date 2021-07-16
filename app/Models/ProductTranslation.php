<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductTranslation extends Model
{
    protected $fillable = [
        'product_id',
        'column_name',
        'value',
        'locale',
    ];

    protected $table = 'product_translation';
}
