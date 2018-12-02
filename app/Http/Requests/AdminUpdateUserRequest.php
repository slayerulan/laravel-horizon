<?php

namespace App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;
use App\User;
use Session;
use Illuminate\Validation\Rule;

class AdminUpdateUserRequest extends FormRequest
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
        $user_id = $request->user_id;
        return [
          // 'full_name' => 'required', 
          'username' => "required",
          // 'email'  => "required|max:50|unique:users,email,{$user_id},id,deleted_at,NULL",
          // 'password' => 'confirmed',
          // 'profile_image' => 'max:1000000|mimes:jpeg,jpg,bmp,png'
        ];
    }
}
