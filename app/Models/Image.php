<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Image
 * @package App\Models
 */
class Image extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'source_id',
        'source_type',
        'column_name',
        'url',
    ];
}
