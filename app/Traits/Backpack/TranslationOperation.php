<?php

namespace App\Traits\Backpack;

use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Str;

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

            foreach ($item->getTranslations() as $columnName => $translation) {

                foreach ($translation as $locale => $value) {

                    if (isset($fields[sprintf('%s_t_%s', $columnName, $locale)])) {

                        $fields[sprintf('%s_t_%s', $columnName, $locale)]['value'] = $value;

                    }
                }
            }
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
     * @noinspection PhpFullyQualifiedNameUsageInspection
     * @noinspection PhpUndefinedClassInspection
     */
    public function store()
    {
        $this->crud->hasAccessOrFail('create');

        // execute the FormRequest authorization and validation, if one is required
        $this->crud->validateRequest();

        $inputData = $this->crud->getStrippedSaveRequest();

        dd($inputData);

        // insert item in the db
        $item = $this->crud->create($inputData);
        $this->data['entry'] = $this->crud->entry = $item;

        $this->setItemTranslation($item, $inputData);

        // show a success message
        \Alert::success(trans('backpack::crud.insert_success'))->flash();

        // save the redirect choice for next time
        $this->crud->setSaveAction();

        return $this->crud->performSaveAction($item->getKey());
    }

    /**
     * @param $item
     * @param $inputData
     */
    private function setItemTranslation($item, $inputData)
    {
        if (isset($item->translatable) && is_array($item->translatable) && method_exists($item, 'setTranslation')) {

            foreach ($item->translatable as $column) {

                foreach ($inputData as $key => $value) {

                    if (strpos($key, sprintf('%s_t_', $column)) === 0) {

                        $locale = Str::substr($key, Str::length(sprintf('%s_t_', $column)));

                        $item->setTranslation($locale, $column, $value);
                    }
                }
            }
        }
    }

    /**
     * @return mixed
     * @noinspection PhpUndefinedClassInspection
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

        $this->setItemTranslation($item, $inputData);

        // show a success message
        \Alert::success(trans('backpack::crud.update_success'))->flash();

        // save the redirect choice for next time
        $this->crud->setSaveAction();

        return $this->crud->performSaveAction($item->getKey());
    }
}
