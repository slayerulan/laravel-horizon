<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlayerWalletRequest extends FormRequest
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
            'user_id'  => 'required',
            'amount' => "required|regex:/^\d*(\.\d{1,2})?$/",
        ];
    }
    public function messages()
    {
        return [
            'user_id.required' => 'Player is required',
            'amount.required'  => 'Chips value is required',
            'amount.regex'  => 'Chips format is invalid',
        ];
    }
}
