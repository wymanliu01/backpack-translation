<?php

namespace App\Traits\Backpack;

use Exception;
use Str;

/**
 * Trait ImageOperation
 * @package App\Traits\Backpack
 */
trait ImageOperation
{
    /**
     * @param $instance
     * @param $inputData
     * @throws Exception
     * @noinspection PhpUnusedPrivateMethodInspection
     */
    private function saveImageFields($instance, $inputData)
    {
        foreach ($instance->imageable as $column) {

            $expectedMethod = 'set' . Str::ucfirst(Str::camel($column)) . 'Attribute';

            if (!method_exists($instance, $expectedMethod)) {
                $class = get_class($instance);
                throw new Exception("Method $expectedMethod in class $class is not set up correctly");
            }
        }

        foreach ($instance->imageable as $column) {
            $instance->$column = $inputData[$column];
        }
    }
}
