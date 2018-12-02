<?php

namespace App\Http\Traits\Api;

use DB;
use Illuminate\Support\Facades\Session;

/**
 * This will contain all functions that will help to call the Apis of Conflux
 *
 *  @author Arijit Jana
 */
trait ProviderApiTrait
{
    /**
     * calls the authentication api to get user details from conflux
     * @param  Request     $request     authentication token from conflux
     * @return array           details of the user logged in
     */
    public function authenticateUser($token) {
        $data['operatorId'] = OPERATOR_ID;
        $data['token']      = $token;
        $data['hash']       = md5(OPERATOR_ID.$token.SITE_PASS_KEY);
        $data_json          = json_encode($data);
        $url                = CONFLUX_API_URL."authenticate";
        $response           = hostApiCall($url,$data_json);
        return $response;
    }

    /**
     * sends bet request to Conflux
     * @param  Request     $request     details of the bet that a user want to place
     * @return html           redirects to the apex site
     */
    public function sendBetRequest($ticketId, $amount, $betInfo) {
        $data['sessionToken']   = $this->sessionToken;
        $data['ticketId']       = $ticketId;
        $data['amount']         = $amount;
        $data['betInfo']        = $betInfo;
        $data['currency']       = Session::get('conf_user_details')->currency;
        $data['playerId']       = Session::get('conf_user_details')->playerId;
        $data['hash']           = md5($this->sessionToken.$ticketId.$amount.$betInfo.$data['currency'].Session::get('conf_user_details')->playerId.SITE_PASS_KEY);
        $data_json              = json_encode($data);
        $url                    = CONFLUX_API_URL."bet";
        $response               = hostApiCall($url,$data_json);
        return $response;
    }

    /**
     * sends bet win amount
     * @param  string     $bet_number     unique number of the bet
     * @param  integer    $amount         bet win amount
     * @return boolean           response from the conflux
     */
    public function sendWinBet($bet_number, $amount, $currency, $playerId) {
        $data['ticketId']   = $bet_number;
        $data['currency']   = $currency;
        $data['amount']     = $amount;
        $data['playerId']   = $playerId;
        $data['hash']       = md5($bet_number.$data['currency'].$amount.$playerId.SITE_PASS_KEY);
        $data_json          = json_encode($data);
        $url                = CONFLUX_API_URL."result";
        $response           = hostApiCall($url,$data_json);
        return $response;
    }

    /**
     * sends bet refund amount
     * @param  string     $bet_number     unique number of the bet
     * @param  integer    $amount         bet win amount
     * @return boolean           response from the conflux
     */
    public function sendRefundBet($bet_number, $amount, $currency, $playerId) {
        $data['ticketId']   = $bet_number;
        $data['currency']   = $currency;
        $data['amount']     = $amount;
        $data['playerId']   = $playerId;
        $data['hash']       = md5($bet_number.$data['currency'].$amount.$playerId.SITE_PASS_KEY);
        $data_json          = json_encode($data);
        $url                = CONFLUX_API_URL."refund";
        $response           = hostApiCall($url,$data_json);
        return $response;
    }

    /**
     * sends bet cancel amount
     * @param  string     $bet_number     unique number of the bet
     * @param  integer    $amount         bet win amount
     * @return boolean           response from the conflux
     */
    public function sendCancelBet($bet_number, $amount, $currency, $playerId) {
        $data['ticketId']   = $bet_number;
        $data['currency']   = $currency;
        $data['amount']     = $amount;
        $data['playerId']   = $playerId;
        $data['hash']       = md5($bet_number.$data['currency'].$amount.$playerId.SITE_PASS_KEY);
        $data_json          = json_encode($data);
        $url                = CONFLUX_API_URL."cancel";
        $response           = hostApiCall($url,$data_json);
        return $response;
    }
}
