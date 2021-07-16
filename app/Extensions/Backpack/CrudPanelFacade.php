<?php

namespace App\Extensions\Backpack;

use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * @method static setModel(string $class)
 * @method static setRoute(string $string)
 * @method static setEntityNameStrings(string $string, string $string1)
 * @method static column(string $string)
 * @method static setValidation(string $class)
 * @method static field(string $string)
 */
class CrudPanelFacade extends CRUD
{
    public static function addField(array $config): CrudPanel
    {
        $defaultField = CRUD::addField($config);

        if (isset($config['translatable']) && is_array($config['translatable'])) {

            foreach ($config['translatable'] as $key) {

                $localeConfig = $config;

                if (isset($localeConfig['name'])) {

                    $localeConfig['name'] = sprintf('%s_t_%s', $localeConfig['name'], $key);
                }

                if (isset($localeConfig['label'])) {

                    $localeConfig['label'] = sprintf('%s (%s)', $localeConfig['label'], $key);
                }

                CRUD::addField($localeConfig);

            }

        }

        return $defaultField;
    }

    public static function addTranslationSection(array $fields, array $locales)
    {
        CRUD::addField([
            'name' => 'tran_title',
            'type' => 'custom_html',
            'value' => '<h3>Default Locale</h3>',
            'wrapper' => ['class' => 'form-group col-sm-12 mt-5'],
        ]);

        foreach ($fields as $config) {

            CRUD::addField($config);
        }

        CRUD::addField([
            'name' => 'separator',
            'type' => 'custom_html',
            'value' => '<hr>',
            'wrapper' => ['class' => 'form-group col-sm-12 mt-3'],
        ]);

        foreach ($locales as $key) {

            CRUD::addField([
                'name' => 'tran_title' . $key,
                'type' => 'custom_html',
                'value' => '<h3>Locale (' . $key . ')</h3>'
            ]);

            foreach ($fields as $config) {

                $localeConfig = $config;

                if (isset($localeConfig['name'])) {

                    $localeConfig['name'] = sprintf('%s_t_%s', $localeConfig['name'], $key);
                }

                if (isset($localeConfig['label'])) {

                    $localeConfig['label'] = sprintf('%s (%s)', $localeConfig['label'], $key);
                }

                CRUD::addField($localeConfig);
            }

            CRUD::addField([
                'name' => 'separator' . $key,
                'type' => 'custom_html',
                'value' => '<hr>'
            ]);
        }
    }
}
