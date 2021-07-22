<?php

namespace App\Traits\Backpack;

use Alert;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Exception;

/**
 * Trait BackpackOperation
 * @package App\Traits\Backpack
 */
trait BackpackOperation
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;
    use ShowOperation;

    /**
     * @return mixed
     * @throws Exception
     */
    public function store()
    {
        $this->crud->hasAccessOrFail('create');

        // insert item in the db
        $item = $this->crud->create($this->crud->getStrippedSaveRequest());
        $this->data['entry'] = $this->crud->entry = $item;

        $this->processExtraStoreOperation();

        // show a success message
        Alert::success(trans('backpack::crud.insert_success'))->flash();

        // save the redirect choice for next time
        $this->crud->setSaveAction();

        return $this->crud->performSaveAction($item->getKey());
    }

    /**
     * @throws Exception
     */
    private function processExtraStoreOperation()
    {
        if (in_array(ImageOperation::class, class_uses($this))) {
            if (!is_array($this->data['entry']->imageable)) {
                $class = get_class($this->data['entry']);
                throw new Exception("Attribute \$imageable in class $class is not set up correctly");
            }
            $this->saveImageFields($this->data['entry'], $this->crud->getStrippedSaveRequest());
        }
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function update()
    {
        $this->crud->hasAccessOrFail('update');

        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();
        // update the row in the db
        $item = $this->crud->update($request->get($this->crud->model->getKeyName()),
            $this->crud->getStrippedSaveRequest());
        $this->data['entry'] = $this->crud->entry = $item;

        $this->processExtraStoreOperation();

        // show a success message
        Alert::success(trans('backpack::crud.update_success'))->flash();

        // save the redirect choice for next time
        $this->crud->setSaveAction();

        return $this->crud->performSaveAction($item->getKey());
    }
}
