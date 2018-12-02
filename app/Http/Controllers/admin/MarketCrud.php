<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Crud;
use App\Http\Controllers\Controller;
use App\Http\Requests\MarketRequest;
use App\Http\Traits\DataSaver;
use App\FeedModel\Market;
use App\FeedModel\Sport;
use Datatables;

/**
 *  This is a command to fetch all markets and save into database from admin panel.
 *
 *  @author	Sourav Chowdhury
 */
class MarketCrud extends Crud
{
	use DataSaver;
	/**
	 * name of the table . REQUIRED
	 * @var string
	 */
	public $table_name 				= 'markets';
	public $model_path 				= 'App\FeedModel\\';
	/**
	 * route name that shold be used to create different action link. REQUIRED
	 * @var string
	 */
	public $route_slug 				= 'admin-sports-book-management-market-';
	/**
	 * You can use RBAC to manage action button by crud. OPTIONAL
	 * @var bool
	 */
	public $use_rbac				= false;
	/**
	 * You can customize you table coloumn.
	 *  field name as key, label as value. only table field are acceptable. OPTIONAL
	 * @var array
	 */
	//public $columns_list;
	/**
	 * You can unset action button. 'view/edit/delete acceptable'. OPTIONAL
	 * @var array
	 */
	public $unset_actions_button	= ['edit','view','delete'];

	public $unset_coloumn = ['id','market_key','odds','has_extra','created_at','updated_at','updated_by'];

	/**
	 * This will display table data in view page in data table
	 * @return view           	 load view page
	 */
    public function show()
    {
				$this->setLeftSideBarData();
				return view('admin.sports-book.market.list',['profile_image' => $this->profile_image, 'parent_menu' => $this->parent_menu, 'sub_menu' =>$this->sub_menu]);
    }

		/**
	 * This will display table data in view page in data table using ajax
	 * @return view           	 load view page
	 */
		public function getAllMarkets()
		{
					$market = Market::with('sport');
					return Datatables::of($market)
						->editColumn('sport_id', function(Market $market) {
	        		return $market->sport['name'];
	       		})
						->editColumn('status', function(Market $market)
						 {
							 if($market->status == 'active')
										return '<span class="badge bg-green"> active </span>';
							 else{
										return '<span class="badge bg-red"> inactive </span>';
									 }
						  })
						// ->filterColumn('sports.name', function($query, $keyword)
						// {
    				// 	$query->havingRaw('LOWER(sports.name) LIKE ?', ["%{$keyword}%"]);
        		// })
						->addColumn('action', function (Market $market) {
								$view_url = url('/').'/apex-site-admin/sports-book-management/market/view/'.$market->id;
								$edit_url = url('/').'/apex-site-admin/sports-book-management/market/edit/'.$market->id;
                return '<a href="'.$view_url.'" class="waves-effect btn btn-info"><i class="material-icons">info</i> View</a>
								<a href="'.$edit_url.'" class="waves-effect btn btn-warning"><i class="material-icons">create</i> Edit</a>
                ';
            })
						->rawColumns(['status','action'])
						->orderColumns(['id'], '-:column $1')
	       		->make(true);
		}
	/**
	 * This will display a details for an id of this table
	 * @param  integer  $id      id of selected row
	 * @return view           	 load view page
	 */
	public function view($id)
	{
			$this->page_title = 'View Market';
			$data = $this->rendarView($id);
			$data['label_data']['sport_id'] = 'Sport';
			return view('admin.crud.view',$data);
	}
	/**
	 * This will load an insert form for current table
	 * @return view   load view page
	 */
	public function add()
	{
		$this->page_title = 'Add Market';
		$data = $this->rendarAdd();
		$data['input_list']['sport_id']['field_label'] = 'Sport';
		return view('admin.crud.form',$data);
	}
	/**
	 * This will insert data into databse
	 * @param  MarketRequest $request validated form request
	 * @return void                 redirect page
	 */
	public function insert(MarketRequest $request)
	{
		$user_id = Session::get('user_id');
		$this->log('Market Added From Admin',$user_id);

		$response = $this->insertData($request);
		return redirect($response);
	}
	/**
	 * this will load edit form
	 * @param  integer $id id of this table
	 * @return view     load edit form
	 */
	public function edit($id)
	{
		$this->page_title = 'Edit Market';
		$data = $this->rendarEdit($id);
		$this->changeFieldType('sport_id','select','Sport',null,null,'show-tick form-control','disabled');
		$this->changeFieldType('name','text','Name',null,null,'form-control','readonly');
		$this->changeFieldType('market_group','text','Market Group',null,null,'form-control','readonly');
		$data = $this->rendarEdit($id);
		$sport_id = $data['input_list']['sport_id']['default_value'];
		$data['input_list']['sport_id']['field_label'] = 'Sport';
		return view('admin.crud.form',$data);
	}
	/**
	 * this will update a row
	 * @param  MarketRequest $request validated form request
	 * @return void                 redirect page
	 */
	public function update(MarketRequest $request)
	{
		$market = Market::find($request['id']);
		$sport_id = $market['sport_id'];
		$name = $market['name'];
		$market_group = $market['market_group'];

		$request['sport_id'] = $sport_id;
		$request['name'] = $name;
		$request['market_group'] = $market_group;

		$user_id = Session::get('user_id');
		$this->log('Market Updated From Admin',$user_id);

		$response = $this->updateData($request);
		return redirect($response);
	}
	/**
	 * this will delete a row
	 * @param  inetger $id id or row to be deleted
	 * @return void     redirect to list page
	 */
	public function delete($id)
	{
		$user_id = Session::get('user_id');
		$this->log('Market Deleted From Admin',$user_id);

		$response = $this->deleteData($id);
		return redirect($response);
	}
	/**
	 * If you want to call any function for all, set here. by default crud will call this
	 * @return void        called by crud self
	 */
	public function callDefault()
	{
		//$this->setRelation('sport_id', 'sports', 'id');
		//$this->changeFieldType('role_id','muliselect','Role Name');
		//$this->addCallBackColoumn('field', 'label', 'callbackFunctionName');
		$where = "WHERE A.deleted_at is null";
		$this->additional_where   = $where;
		$this->unsetAdd();
	}
	/**
	 * this is a demo callback function, must send $value for default callback
	 * @param mixed $row_data  current row object/array
	 * @param mixed $value    callback field current value
	 * @param string $type     list/view/insert/update
	 */
	/*public function setStatus($row_data,$value,$type)
	{
		if($type =="list" || $type =="view"){
			$type = ['active'=> 'bg-green', 'inactive' => 'bg-red','1'=>'bg-green','0'=>'bg-red'];
			$value = isset($type[$value]) ? $type[$value] : 'bg-grey';
			return '<span class="badge '.$value.'"> '.$row_data->status.' </span>';
		}
		return $value;
	}*/
}
