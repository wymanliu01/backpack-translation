<?php

namespace App\Interfaces\Backpack;

interface WithTranslation
{
    public function setTranslation($locale, $column, $value): void;

    public function getTranslations(): array;
}
