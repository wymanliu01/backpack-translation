<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Traits\Backpack\TranslationOperation;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ProductCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class ProductCrudController extends CrudController
{
    use DeleteOperation;
    use ShowOperation;
    use TranslationOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(Product::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/product');
        CRUD::setEntityNameStrings('product', 'products');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('id');
        CRUD::column('sku');
        CRUD::column('name');
        CRUD::column('description');
        CRUD::column('price');
        CRUD::column('created_at');
        CRUD::column('updated_at');

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     * @noinspection PhpUnused
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ProductRequest::class);

        CRUD::addField([
            'name' => 'sku',
            'type' => 'text',
            'label' => 'SKU',
        ]);

        CRUD::addField([
            'name' => 'name',
            'type' => 'text',
            'label' => 'Product Name (Default)',
        ]);

        CRUD::addField([
            'name' => 'description',
            'type' => 'textarea',
            'label' => 'Description (Default)',
        ]);

        CRUD::addField([
            'name' => 'translations',
            'label' => 'Translations',
            'type' => 'translations',
            'fields' => [
                [
                    'name' => 'name',
                    'type' => 'text',
                    'label' => 'Product Name',
                ],
                [
                    'name' => 'description',
                    'type' => 'textarea',
                    'label' => 'Description',
                ],
            ],
        ]);

        CRUD::addField([
            'name' => 'price',
            'type' => 'number',
            'label' => 'Price',
        ]);

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
    }
}
