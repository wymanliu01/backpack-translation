<?php

namespace App\Models;

use App\Traits\HasImageModel;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProductTranslation
 * @package App\Models
 */
class ProductTranslation extends Model
{
    use HasImageModel;

    /**
     * @var string[]
     */
    protected $fillable = [
        'product_id',
        'locale',
        'name',
        'description',
    ];

    /**
     * @var string
     */
    protected $table = 'product_translation';

    /**
     * @param $value
     * @noinspection PhpUnused
     */

}
