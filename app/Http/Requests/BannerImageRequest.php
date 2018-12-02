<?php

namespace App\Http\Requests;
use Illuminate\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

class BannerImageRequest extends FormRequest
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
          'title'  => 'required|max:50|unique:banner_images,title,'.$request->id,
          'image' => 'required|max:1000000|mimes:jpeg,jpg,bmp,png|dimensions:width=850,height=205',
          'status'  => 'required',
        ];
    }
}
