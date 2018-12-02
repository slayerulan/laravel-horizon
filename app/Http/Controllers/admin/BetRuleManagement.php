<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\admin\AdminBaseController;
use App\Http\Requests\AdminBetRuleRequest;
use App\BetRule;
use App\User;
use App\FeedModel\Bookmaker;
use App\Currency;

/**
 *  this is bet rule management controller. Basically manage crud operation depend on role_id
 *
 *  @author Anirban Saha
 */
class BetRuleManagement extends AdminBaseController
{
	/**
	 *  it contains field name. These are the key to set rule.
	 *
	 *  @var  array
	 */
	public static $rules = [
		'parlay_min_limit','parlay_max_limit','straight_bet_min_limit','straight_bet_max_limit','maximum_parlay_payout','maximum_straight_bet_payout',
		'maximum_number_of_bets_per_parlay', 'minimum_number_of_bets_per_parlay'
	];
	/**
	 * this will load insert form to add new bet rule
	 */
	public function add()
	{
		$data				= 	$this->getLeftSideBarData();
		$data				= array_merge($this->getRuleDetailsData(),$data);
		// $bookmaker = Bookmaker::where('status','active')->get();
		// $data['bookmaker']	= 	$bookmaker;
		$data['page_title']	= 	'Add Bet Rule';
		$data['save_url']	=	route('admin-settings-bet-rules-insert');
		if($this->role_id == 1){
			$data['show_default_field'] = true;
		}
		else{
			$data['show_default_field'] = false;
		}
		return view('admin.bet_rules.form',$data);
	}
	/**
	 * this will insert a new bet rule into database depend on logged in user
	 * @param  AdminBetRuleRequest $request validated request
	 * @return void                       redirect
	 */
	public function insert(AdminBetRuleRequest $request)
	{
		$data['user_id'] 		= $this->role_id == 1 ? null : $this->user_id;
		$data['created_by'] 	= $this->user_id;
		$data['title'] 			= $request->title;
		if (isset($request->is_default)) {
			$data['is_default'] = $request->is_default;
		}
		$rules 					= 	self::$rules;
		$rule_array				=	[];

		foreach ($rules as $key => $value) {
			if (is_array($request->{$value})) {
				foreach ($request->{$value} as $currency_key => $value_per_currency) {
					$rule_array[$value][$currency_key] = $value_per_currency;
				}
			}
			else{
				$rule_array[$value] = $request->{$value};
			}
		}

		$data['rule'] 			= json_encode($rule_array);
		BetRule::create($data);
		$this->log('created Bet rule');
		$this->setFlashAlert('success', 'New Rule Added');
		return redirect(route('admin-settings-bet-rules-list'));
	}
	/**
	 *  This will load bet rule table view
	 *
	 *  @return  string  return html view page
	 */
	public function show()
	{
		$data 				= 	$this->getLeftSideBarData();
		$data['add']		= $this->canAdd( 'bet-rules-list');
		$data['modify']		= $this->canModify( 'bet-rules-list');
		if($this->role_id == 1){
			$all_rules = BetRule::with('user')->get();
		}else if( $data['modify']){
			$all_rules = BetRule::where('user_id',$this->user_id)->orWhereNull('user_id')->with('user')->get();
		}else {
			$all_rules	= User::find($this->user_id)->bet_rule;
		}
		$data['all_rules']	=	$all_rules;
		$data['title']	= 	'Bet Rule';
		return view('admin.bet_rules.table',$data);
	}
	/**
	 *  This will show details of a bet rule. If basic agent logged in
	 *  Load his rule by default
	 *
	 *  @param   int  $id id of bet rule
	 *  @return  string  load html view page
	 */
	public function view($id)
	{
		$data 				= 	$this->getLeftSideBarData();
		$rule_details		=	$this->getRuleDetails($id);

        $value = json_decode($rule_details->rule);
		// $bookmaker = Bookmaker::where('id',$value->bookmaker)
  //                  ->get()->toArray();
		// $bookmaker_name = $bookmaker[0]['name'];

		if(isset($rule_details->id)){
		  // $data['bookmaker_name']	= $bookmaker_name;
			$back_url = route('admin-settings-bet-rules-list');
			$data['back_url']	= $back_url;
			$data['rules']			= json_decode($rule_details->rule);
			$data['page_title']		= $rule_details->title;
			return view('admin.bet_rules.view',$data);
		}else {
			$this->setFlashAlert('danger','Access Denied!');
			return back();
		}
	}
	/**
	 *  this will load edit form
	 *
	 *  @param   int  $id id of bet rule
	 *  @return  string  load html view page
	 */
	public function edit($id)
	{
		$rule_details		=	$this->getRuleDetails($id);

    	// $bookmaker = Bookmaker::where('status', 'active')->get()->toArray();

		if(isset($rule_details->id)){
			$data				= 	$this->getLeftSideBarData();
			$data				= array_merge($this->getRuleDetailsData($id),$data);
			$data['page_title']		= 	'Edit Bet Rule';
			$data['save_url']		=	route('admin-settings-bet-rules-update');
      		// $data['bookmaker'] = $bookmaker;
      		if($this->role_id == 1){
				$data['show_default_field'] = true;
			}
			else{
				$data['show_default_field'] = false;
			}
			return view('admin.bet_rules.form',$data);
		}else {
			$this->setFlashAlert('danger','Access Denied!');
			return back();
		}
	}
	/**
	 *  this will update a bet rule
	 *
	 *  @param   AdminBetRuleRequest  $request  validated request data
	 *  @return  void               redirect to listing page
	 */
	public function update(AdminBetRuleRequest $request)
	{
		$url = url()->previous();
		$id = str_after($url, 'edit/');
		$rule_details		=	$this->getRuleDetails($id);
		if(isset($rule_details->id)){
			$data['created_by'] 	= $this->user_id;
			$data['title'] 			= $request->title;
			if (isset($request->is_default)) {
				$data['is_default'] = $request->is_default;
			}
			$rules 					= self::$rules;
			$rule_array				=  [];
			foreach ($rules as $key => $value) {
				$rule_array[$value] = $request->{$value};
			}
			$data['rule'] 			= json_encode($rule_array);
			BetRule::where('id',$id)->update($data);
			$this->log('updated Bet rule');
			$this->setFlashAlert('success', 'Rule Updated');
		}else{
			$this->setFlashAlert('danger','Access Denied!');
		}
		return redirect(route('admin-settings-bet-rules-list'));

	}
	/**
	 *  this will delete a bet rule
	 *
	 *  @param   int  $id  id of bet rule
	 *  @return  void  redirect to list page
	 */
	public function delete($id)
	{
		$rule_details		=	$this->getRuleDetails($id);
		if(isset($rule_details->id)){
			$bet_rule = BetRule::find($id);
			if (empty($bet_rule->user)) {
				$bet_rule->delete();
				$this->log('Deleted Bet rule');
				$this->setFlashAlert('success', 'Bet rule deleted');
			}
			else{
				$this->setFlashAlert('danger',"Can't delete a bet rule that is assigned to user.");
			}
		}else{
			$this->setFlashAlert('danger','Access Denied!');
		}
		return redirect(route('admin-settings-bet-rules-list'));
	}

	/**
	 *  it will return bet rule details, if logged in user has permission to perform that action
	 *
	 *  @param   int  $id      id of bet rule
	 *  @param   string  $action  by default modify action, need to send for other
	 *  @return  bool|object  false if permission denied else bet rule object
	 */
	public function getRuleDetails($id,$action=null)
	{
		if($this->role_id == 1){
			$rule_details = BetRule::where('id',$id)->with('user')->first();
		}else if($this->canModify( 'bet-rules-list')){
			$rule_details = BetRule::where('id',$id)->with('user')->first();
			//only modify self created rule
			if($action == null && property_exists($rule_details,'exists') && $rule_details->user_id != $this->user_id) {
				return false;
			}
		}else {
			$rule_details	= User::find($this->user_id)->bet_rule;
			// $rule_details 	= BetRule::find($user_details->bet_rule_id);
		}
		return $rule_details;
	}

	/**
	 * this will create an array from bet rule json string
	 *
	 *  @param   int  $id  bet rule id
	 *  @return  object  bet rule details in field value pair
	 */
	public function getRuleDetailsData($id=null)
	{
		$data['rule_details'] 	= false;
		$data['rules']			= self::$rules;
		$data['currencies']		= Currency::active()->get()->toArray();
		// a($data['currencies']);
		if($id !== null){
			$rule_details = $this->getRuleDetails($id);
		}
		$rule_details_array 			= [];
		$rule_details_array['title'] 	= '';
		if(isset($rule_details->id)){
			$rule_details_array['id'] 	= $rule_details->id;
			$rule_details_array['title'] 	= $rule_details->title;
			$rule_details_array['is_default'] 	= $rule_details->is_default;
			$rule_details			= 	json_decode($rule_details->rule);
		}
		foreach ($data['rules'] as $key => $value) {
			$rule_details_array[$value] = isset($rule_details->$value) ? $rule_details->$value : '';
		}
		$data['rule_details'] 	= 	(object)$rule_details_array;

		return $data;
	}
}
