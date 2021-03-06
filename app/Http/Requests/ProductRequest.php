<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

//testing
class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'sku' => 'required',
            'name' => 'required',
            'price' => 'required',
            'translations.*.locale' => 'required',
            'translations.*.name' => 'required',
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            //
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            //
        ];
    }

    protected function prepareForValidation()
    {
        $translations = [];

        foreach (json_decode($this->input('translations'), true) as $translation) {
            $translations[$translation['locale']] = $translation;
        }

        $this->merge(['translations' => $translations]);
    }
}
