<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BetSettingRequest extends FormRequest
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
            'default_sports_slug' => 'required',
            'default_bookmaker' => 'required',
            'hide_minute'  => 'required|integer',
            'maximum_hour'  => 'required',
            //'maximum_league_selection'  => 'required|integer',
        ];
    }
}
