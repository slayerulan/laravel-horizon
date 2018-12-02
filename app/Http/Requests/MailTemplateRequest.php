<?php

namespace App\Http\Requests;
use Illuminate\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

class MailTemplateRequest extends FormRequest
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
            'name' => 'required|max:50|unique:mail_templates,name,'.$request->id,
            'subject' => 'required',
            'content' => 'required',
            'status' => 'required'
        ];
    }
}
