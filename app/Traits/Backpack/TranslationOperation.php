<?php /** @noinspection PhpUndefinedClassInspection */

namespace App\Traits\Backpack;

use Alert;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

/**
 * Trait CreateWithTranslationOperation
 * @package App\Traits
 */
trait TranslationOperation
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;

    /**
     * @param $id
     * @return Application|Factory|View
     */
    public function edit($id)
    {
        $this->crud->hasAccessOrFail('update');
        // get entry ID from Request (makes sure its the last ID for nested resources)
        $id = $this->crud->getCurrentEntryId() ?? $id;

        $fields = $this->crud->getUpdateFields();

        $item = $this->data['entry'] = $this->crud->getEntry($id);

        if (method_exists($item, 'getTranslations')) {

            $fields['translations']['value'] = json_encode($item->getTranslations());
        }

        $this->crud->setOperationSetting('fields', $fields);
        // get the info for that entry

        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->crud->getSaveAction();
        $this->data['title'] = $this->crud->getTitle() ?? trans('backpack::crud.edit') . ' ' . $this->crud->entity_name;

        $this->data['id'] = $id;

        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view($this->crud->getEditView(), $this->data);
    }

    /**
     * @return mixed
     */
    public function store()
    {
        $this->crud->hasAccessOrFail('create');

        // execute the FormRequest authorization and validation, if one is required
        $this->crud->validateRequest();

        $inputData = $this->crud->getStrippedSaveRequest();

        // insert item in the db
        $item = $this->crud->create($inputData);
        $this->data['entry'] = $this->crud->entry = $item;

        if (!empty($inputData['translations'])) {
            $this->setItemTranslations($item, json_decode($inputData['translations']));
        }

        // show a success message
        Alert::success(trans('backpack::crud.insert_success'))->flash();

        // save the redirect choice for next time
        $this->crud->setSaveAction();

        return $this->crud->performSaveAction($item->getKey());
    }

    /**
     * @param $item
     * @param $locale
     * @param $columnName
     * @param $value
     */
    private function setItemTranslations($item, $translations)
    {
        $item->setTranslations($translations);
    }

    /**
     * @return mixed
     */
    public function update()
    {
        $this->crud->hasAccessOrFail('update');

        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();
        // update the row in the db

        $inputData = $this->crud->getStrippedSaveRequest();

        $item = $this->crud->update($request->get($this->crud->model->getKeyName()), $inputData);
        $this->data['entry'] = $this->crud->entry = $item;

        if (!empty($inputData['translations'])) {
            $this->setItemTranslations($item, json_decode($inputData['translations']));
        }

        // show a success message
        Alert::success(trans('backpack::crud.update_success'))->flash();

        // save the redirect choice for next time
        $this->crud->setSaveAction();

        return $this->crud->performSaveAction($item->getKey());
    }
}
