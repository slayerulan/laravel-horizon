<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FrontendRegistrationRequest extends FormRequest
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
        return [
			'full_name'  			=> 'required|max:50',
			'username'  			=> 'required|unique:users|max:50|alpha_num',
			'password'  			=> 'required|max:15|min:8|regex:/^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9]+)$/',
			'confirm_password'  	=> 'same:password',
            'email'  				=> 'required|unique:users|max:30|email|',
            'mobile'  				=> 'required|digits:10',
            'gender'  				=> 'required|in:male,female',
            'address'  				=> 'required|min:5',
            'country'  				=> 'required|integer',
            'language'  			=> 'required|integer',
            'currency'  			=> 'required|integer',
            'profile_image'  		=> 'mimetypes:image/jpeg,image/jpg,image/png'
        ];
    }
	public function messages()
	{
	    return [
	        'profile_image.mimetypes' => 'we accept jpeg,jpg,png format only',
	        'password.regex'  		  => 'password must contain atleast one number and one letter',
	    ];
	}
}
