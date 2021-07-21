<?php

namespace App\Traits\Backpack;

trait ImageOperation
{
    private function saveImageFields($instance, $inputData)
    {
        foreach ($instance->imageable as $column) {
            $instance->$column = $inputData[$column];
        }
    }
}
