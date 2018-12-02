<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\admin\AdminBaseController;
use App\Http\Requests\BetSettingRequest;
use App\BetSetting;
use App\FeedModel\Bookmaker;
use App\User;
use File;

/**
 *  this is bet settings management controller.
 *
 *  @author Sourav Chowdhury
 */
class BetSettingManagement extends AdminBaseController
{
	/**
	 *  it contains field name. These are the key to set rule.
	 *
	 *  @var  array
	 */
	public static $rules = ['default_sports_slug','default_bookmaker','hide_minute','maximum_hour'];
	/**
	 * this will load insert form to add new bet rule
	 */
	public function add()
	{
		$data				= 	$this->getLeftSideBarData();
		$data				= array_merge($this->getRuleDetailsData(),$data);
		$data['page_title']	= 	'Add Default Settings';
		//$data['save_url']	=	route('admin-settings-bet-rules-insert');
		//return view('admin.bet_rules.form',$data);
	}
	/**
	 * this will insert a new bet rule into database depend on logged in user
	 * @param  AdminBetRuleRequest $request validated request
	 * @return void                       redirect
	 */
	public function insert(AdminBetRuleRequest $request)
	{
		$rules 					= 	self::$rules;
		$rule_array				=	[];
		foreach ($rules as $key => $value) {
			$rule_array[$value] = $request->{$value};
		}
		$data['rule'] 			= json_encode($rule_array);
		AdminBetSettingRequest::create($data);
		$this->log('created Bet settings');
		$this->setFlashAlert('success', 'New Default rule Inserted');
		return redirect(route('admin-sports-book-management-bet-settings-list'));
	}
	/**
	 *  This will load bet rule table view
	 *
	 *  @return  string  return html view page
	 */
	public function show()
	{
		$data 				= 	$this->getLeftSideBarData();
		//$data['add']		= $this->canAdd( 'bet-rules-list');
		$data['modify']		= $this->canModify( 'bet-settings-list');
		$all_rules = BetSetting::where('created_by',1)->get();
		$data['all_rules']	=	$all_rules;
		$data['page_title']	= 	'Default Settings';
		return view('admin.bet_settings.table',$data);
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
		$value = json_decode($rule_details[0]['rule']);
		$bookmaker = Bookmaker::where('id',$value->default_bookmaker)
                   ->get()->toArray();
		$bookmaker_name = $bookmaker[0]['name'];
		$back_url = route('admin-sports-book-management-bet-settings-list');

			$data['back_url']	= $back_url;
			$data['bookmaker_name']	= $bookmaker_name;
			$data['rules']			= json_decode($rule_details[0]['rule']);
			$data['page_title']		= 'View Default Settings';
			return view('admin.bet_settings.view',$data);
			return back();
	}
	/**
	 *  this will load edit form
	 *
	 *  @param   int  $id id of bet rule
	 *  @return  string  load html view page
	 */
	public function edit($id)
	{
		$bookmaker = Bookmaker::where('status', 'active')
                   ->get()->toArray();

		$rule_details		=	$this->getRuleDetails($id);
		$rule_details		= json_decode($rule_details[0]['rule']);
		$data		= 	$this->getLeftSideBarData();
		$save_url = route('admin-sports-book-management-bet-settings-update');
		return view('admin.bet_settings.form',['page_title' => 'Edit Default Settings','save_url' => $save_url,'bookmaker' => $bookmaker, 'rule_details' => $rule_details, 'profile_image' => $this->profile_image,'parent_menu' => $this->parent_menu, 'sub_menu' =>$this->sub_menu]);
	}
	/**
	 *  this will update a bet rule
	 *
	 *  @param   AdminBetRuleRequest  $request  validated request data
	 *  @return  void               redirect to listing page
	 */
	public function update(BetSettingRequest $request)
	{
		$url = url()->previous();
		$id = str_after($url, 'edit/');
		$rule_details		=	$this->getRuleDetails($id);

			$data['created_by'] 	= $this->user_id;
			$rules 					= self::$rules;

			$rule_array				=  [];
			foreach ($rules as $key => $value) {
				$rule_array[$value] = $request->{$value};
			}
			$constantArr=[
				'default_sports_slug' => $rule_array['default_sports_slug'],
				'default_bookmaker' => (int)$rule_array['default_bookmaker'],
				'hide_minute' => (int)$rule_array['hide_minute'],
				'maximum_hour' => (int)$rule_array['maximum_hour']
				//'maximum_league_selection' => (int)$rule_array['maximum_league_selection'],
				];

			$filename = base_path() . '/config/bet_settings.php';
	        File::put($filename, var_export($constantArr, true));
	        File::prepend($filename, '<?php return ');
	        File::append($filename, ';');

			$data['rule'] 			= json_encode($rule_array);
			BetSetting::where('id',$id)->update($data);
			$this->log('updated Bet Default Settings');
			$this->setFlashAlert('success', 'settings Updated');
		return redirect(route('admin-sports-book-management-bet-settings-list'));

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
			BetRule::find($id)->delete();
			$this->log('Deleted Bet Default rule');
			$this->setFlashAlert('success', 'rule deleted');
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
			$rule_details = BetSetting::where('id',$id)->get()->toArray();
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
		$data['rules']			= 	self::$rules;
		$data['rules'][1] = 'bookmaker';
		if($id !== null){
			$rule_details = $this->getRuleDetails($id);
		}
		$rule_details_array 			= [];
		$rule_details_array['title'] 	= '';
		if(isset($rule_details->id)){
			$rule_details_array['title'] 	= $rule_details->title;
			$rule_details			= 	json_decode($rule_details->rule);
		}
		foreach ($data['rules'] as $key => $value) {
			$rule_details_array[$value] = isset($rule_details->$value) ? $rule_details->$value : '';
		}
		$data['rule_details'] 	= 	(object)$rule_details_array;
		return $data;
	}
}
