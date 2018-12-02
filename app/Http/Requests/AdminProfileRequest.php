<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use App\User;
use Session;
use Illuminate\Validation\Rule;

class AdminProfileRequest extends FormRequest
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
    public function rules()
    {
        $user_id = Session::get('user_id');
        return [
            'full_name'  => 'required',
            'email' => 'required|max:50|unique:users,email,'.$user_id,
            'password' => 'confirmed',
            'profile_image' => 'max:10000|mimes:jpeg,bmp,png'
        ];
    }
}
