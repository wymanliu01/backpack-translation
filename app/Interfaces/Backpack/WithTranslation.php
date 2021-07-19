<?php

namespace App\Interfaces\Backpack;

interface WithTranslation
{
    public function setTranslations($translations): void;

    public function getTranslations(): array;
}
