<?php

namespace App\Http\Requests;
use Illuminate\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

class Cms_pageRequest extends FormRequest
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
            'title'  => 'required|max:50|unique:cms_pages,title,'.$request->id,
            'slug_name'  => 'required|max:50|unique:cms_pages,slug_name,'.$request->id,
            'content'  => 'required',
            'status'  => 'required',
        ];
    }
}
