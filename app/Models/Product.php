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

    public function setTranslations($translations): void
    {
        $locales = [];

        foreach ($translations as $translation) {
            $locales[] = $translation->locale;
        }

        ProductTranslation::whereProductId($this->attributes['id'])->whereNotIn('locale', $locales)->delete();

        foreach ($translations as $translation) {
            ProductTranslation::updateOrCreate([
                'product_id' => $this->attributes['id'],
                'locale' => $translation->locale,
            ], [
                'name' => $translation->name,
                'description' => $translation->description,
            ]);
        }
    }

    public function getTranslations(): array
    {
        $translations = [];

        foreach (ProductTranslation::whereProductId($this->attributes['id'])->get() as $translation) {

            $translations[] = [
                'locale' => $translation->locale,
                'name' => $translation->name,
                'description' => $translation->description,
            ];
        }

        return $translations;
    }
}
