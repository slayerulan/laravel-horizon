<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class userRegistration extends FormRequest
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
    public function rules(Request $request)
    {
        $id = $request->id ? $request->id : 0;
        return [
            'full_name' => 'required',
            'username' => "required|unique:users,username,{$id},id,deleted_at,NULL",
            'email'  => "required|max:50|unique:users,email,{$id},id,deleted_at,NULL",
            'profile_image' => 'max:1000000|mimes:jpeg,jpg,bmp,png'
        ];
    }
}
