<?php

namespace App\Http\Requests;
use Illuminate\Http\Request;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Controllers\admin\BetRuleManagement;
use App\Currency;

class AdminBetRuleRequest extends FormRequest
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
        $currencies = Currency::active()->get()->toArray();
    	$rules 	= 	BetRuleManagement::$rules;

        if(isset($_POST['id']))
        {
            $id = $_POST['id'];
            $title = 'required|unique:bet_rules,title,'.$id;
        }
        else
        {
            $title = 'required|unique:bet_rules,title';
        }
        $data['title'] = $title;
		foreach ($rules as $key => $value){
            if ($value != 'bookmaker' && $value != 'maximum_number_of_bets_per_parlay' && $value != 'minimum_number_of_bets_per_parlay') {
                foreach ($currencies as $currency) {
                    $data[$value.'.'.$currency['curency_name']] = 'required|integer';
                }
            }
            else{
                $data[$value] = 'required|integer';
            }
		}
        return $data;
    }
}
