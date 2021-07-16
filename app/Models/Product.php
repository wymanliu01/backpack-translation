<?php

namespace App\Models;

use App\Interfaces\Backpack\WithTranslation;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Product extends Model implements WithTranslation
{
    use CrudTrait;

    protected $fillable = [
        'sku',
        'name',
        'price',
        'description',
    ];

    public function setTranslation($locale, $column, $value): void
    {
        ProductTranslation::updateOrCreate([
            'product_id' => $this->attributes['id'],
            'locale' => $locale,
            'column_name' => $column,
        ], [
            'value' => $value,
        ]);
    }

    public function getTranslations(): array
    {
        $translations = [];

        foreach (ProductTranslation::whereProductId($this->attributes['id'])->get() as $translation) {

            $translations[$translation->column_name][$translation->locale] = $translation->value;
        }

        return $translations;
    }
}
