<?php

namespace App\Http\Requests;
use Illuminate\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

class LanguageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(request $request)
    {
        return [
            'slug' 	=> 'required|max:50|unique:languages,slug,'.$request->id,
            'language' => 'required|max:50|unique:languages,language,'.$request->id,
            'status'  => 'required',
        ];
    }
}
