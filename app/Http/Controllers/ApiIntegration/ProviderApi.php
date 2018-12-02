<?php

namespace App\Http\Controllers\ApiIntegration;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\User;
use App\UserProfile;
use App\Http\Traits\Api\ProviderApiTrait;
use App\BetAmountReturn;
use App\ConfluxAgent;
use App\Transaction;
use App\ConfluxUser;
use App\Currency;

class ProviderApi extends Controller
{
    use ProviderApiTrait;

    /**
     * gets a authentication token from Conflux and hit a api to get user details from conflux
     * @param  Request     $request     authentication token from conflux
     * @return html           redirects to the apex site
     */
    public function postAuthentication(Request $request) {
        // echo 'Welcome to Apex';die;
        Session::put('conf_user_details','');
        Session::put('user_details','');
        $response = $this->authenticateUser($request->token);
        // a($response);
        if (isset($response->status) && $response->status == 1 && $response->agent->agentId != '' && $response->agent->agentName != '' && $response->playerId != '' && $response->playerName != '') {
            Session::put('conf_user_details',$response);
            $currency_id = $this->saveCurrency($response->currency);
            $this->saveUserAndAgentDetails($currency_id);
            $this->setFlashAlert('success',__('registration.Successfully Logged In'));
        }
        else{
            $this->setFlashAlert('danger',__("login.Login Unsuccessful, Please note, you can't place bet untill you login properly"));
        }
        Session::put('odds_value_type', 'Decimal');
        return redirect()->route('front-home');
    }

    /**
     * Saves if currency from a response is not present in DB.
     *
     * @param      string       $curency_name       The curency name from response.
     *
     * @return     integer      Id of the currency from the response.
     */
    public function saveCurrency($curency_name) {
        $currency = Currency::firstOrNew(['curency_name' => $curency_name]);
        if (!$currency->id) {
            $currency->curency_name  = $curency_name;
            $currency->status        = 'active';
            $currency->save();
        }
        return $currency->id;
    }

    /**
     * Saves agent and user's username in the DB if already not exist.
     *
     * @param      integer   $currency_id     The currency identifier
     */
    public function saveUserAndAgentDetails($currency_id) {
        $agent_id = '';
        if (Session::get('conf_user_details')->agent->agentId != '') {
            $conflux_agent_id = Session::get('conf_user_details')->agent->agentId;
            $conflux_agent_name = Session::get('conf_user_details')->agent->agentName;
            $agent = User::firstOrNew(['username' => $conflux_agent_name]);
            if (!$agent->id) {
                $agent->role_id      = 3;
                $agent->username     = $conflux_agent_name;
                $agent->currency_id  = $currency_id;
                $agent->status       = 'active';
                $agent->save();

                $conflux_agent                     = new ConfluxAgent;
                $conflux_agent->user_id            = $agent->id;
                $conflux_agent->conflux_agent_id   = $conflux_agent_id;
                $conflux_agent->save();
            }
            $agent_id = $agent->id;
            UserProfile::firstOrCreate(['user_id' => $agent->id]);
        }

        $conflux_user_id = Session::get('conf_user_details')->playerId;
        $conflux_user_name = Session::get('conf_user_details')->playerName;
        $user = User::firstOrNew(['username' => $conflux_user_name]);
        if (!$user->id) {
            $user->role_id      = 4;
            if ($agent_id != '') {
                $user->agent_id = $agent_id;
            }
            $user->username     = $conflux_user_name;
            $user->currency_id  = $currency_id;
            $user->status       = 'active';
            $user->save();

            $conflux_user                    = new ConfluxUser;
            $conflux_user->user_id           = $user->id;
            $conflux_user->conflux_user_id   = $conflux_user_id;
            $conflux_user->save();
        }
        UserProfile::firstOrCreate(['user_id' => $user->id]);
        Session::put('user_details',[
            'user_id' => $user->id,
            'agent_id' => $agent_id,
            'balance' => Session::get('conf_user_details')->balance,
            'bet_rule' => [],
        ]);
        $event = Session::get('conf_user_details')->playerName.' Logged in';
        $this->log($event, $user->id);
    }

    /**
     * Returns the due bet amounts by calling Result request, Refund bet request, Cancel bet request.
     */
    public function returnBetAmount() {
        $return_amount_array = BetAmountReturn::where('status', 'pending')->get();
        if ($return_amount_array) {
            foreach ($return_amount_array as $return_amount) {
                $start_at = date('Y-m-d H:i:s');
                $status = 'error';
                $object = 'send'.ucwords($return_amount->return_type).'Bet';
                $response = $this->{$object}($return_amount->bet_number, $return_amount->amount, $return_amount->currency, $return_amount->player_id);
                if (isset($response->status) && $response->status == 1) {
                    BetAmountReturn::where('id', $return_amount->id)->update(['status' => 'sent']);
                    Transaction::where('id', $return_amount->transaction_id)->update(['status' => 'credited']);
                    $status = 'ok';
                }
                $details = [
                    'Start time'        => $start_at,
                    'bet number'        => $return_amount->bet_number,
                    'player conflux id' => $return_amount->player_id,
                    'transaction_id'    => $return_amount->transaction_id,
                    'amount'            => $return_amount->amount,
                    'currency'          => $return_amount->currency,
                    'stat'              => $response->message,
                    'end_time'          => date('Y-m-d H:i:s'),
                ];
                $insert_array = [
                    'event'     => 'Bet '.$return_amount->return_type.' return',
                    'details'   => json_encode($details),
                    'status'    => $status,
                ];
                $this->logFeed($insert_array);
            }
        }
    }
}
