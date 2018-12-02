<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Session;

class FrontendUpdateProfileRequest extends FormRequest
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
		$id = Session::get('user_details')['id'];
        return [
			'full_name'  			=> 'required|max:50',
            'email'  				=> 'required|unique:users,email,'.$id.'|max:30|email|',
            'mobile'  				=> 'required|digits:10',
            'gender'  				=> 'required|in:male,female',
            'address'  				=> 'required|min:5',
            'country'  				=> 'required|integer',
            'language'  			=> 'required|integer',
            'currency'  			=> 'required|integer',
            'profile_image'  		=> 'mimetypes:image/jpeg,image/jpg,image/png|max:2000'
        ];
    }
}
