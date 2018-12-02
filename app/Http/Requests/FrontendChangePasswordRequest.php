<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class FrontendChangePasswordRequest extends FormRequest
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
		if($request->path() == "reset-password"){
			return [
				'password'  				=> 'required|max:15|min:8',
				'confirm_password'  		=> 'same:password',
			];
		}else{
			return [
				'old_password'  			=> 'required|max:15|min:8',
				'password'  				=> 'required|max:15|min:8',
				'confirm_password'  		=> 'same:password',
			];
		}
    }
}
