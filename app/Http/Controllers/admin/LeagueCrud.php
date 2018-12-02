<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Crud;
use App\Http\Controllers\Controller;
use App\Http\Requests\LeagueRequest;
use App\Http\Traits\DataSaver;
use DB;
use App\FeedModel\League;
use App\FeedModel\Sport;
use App\FeedModel\Country;
use Illuminate\Pagination\Paginator;
use Datatables;
use Illuminate\Support\Facades\Cache;
/**
 *  This is a command to fetch all leagues and save into database from admin panel.
 *
 *  @author	Sourav Chowdhury
 */
class LeagueCrud extends Crud
{
	use DataSaver;

	/**
	 * name of the table . REQUIRED
	 * @var string
	 */
	public $table_name 				= 'leagues';
  public $model_path = 'App\FeedModel\\';
	/**
	 * route name that shold be used to create different action link. REQUIRED
	 * @var string
	 */
	public $route_slug 				= 'admin-sports-book-management-league-settings-';
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

	public $unset_coloumn = ['id','created_at','updated_at','updated_by'];

	/**
	 * This will display table data in view page in data table
	 * @return view           	 load view page
	 */
    public function show()
    {
			$this->setLeftSideBarData();
			return view('admin.sports-book.league.list',['profile_image' => $this->profile_image, 'parent_menu' => $this->parent_menu, 'sub_menu' =>$this->sub_menu]);
    }

		/**
	 * This will display table data in view page in data table using ajax
	 * @return view           	 load view page
	 */
		public function getAllLegues()
		{

					$leagues = League::with('sport','country');
					return Datatables::of($leagues)
						->editColumn('sport_id', function(League $league) {
	        		return $league->sport->name;
	       		})
						->editColumn('country_id', function(League $league) {
	        		return $league->country->name;
	       		})
						->editColumn('status', function(League $league)
						 {
							 if($league->status == 'active')
										return '<span class="badge bg-green"> active </span>';
							 else{
										return '<span class="badge bg-red"> inactive </span>';
									 }
						  })
							// ->filterColumn('sport.name', function($query, $keyword)
 		          // {
     				 	// 		$query->havingRaw('LOWER(sports.name) LIKE ?', ["%{$keyword}%"]);
         		  // })
						->addColumn('action', function (League $league) {
								$view_url = url('/').'/apex-site-admin/sports-book-management/league/view/'.$league->id;
								$edit_url = url('/').'/apex-site-admin/sports-book-management/league/edit/'.$league->id;
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
		$this->page_title = 'View League';
		$data = $this->rendarView($id);
		$data['label_data']['sport_id'] = 'Sport';
		$data['label_data']['country_id'] = 'Country';
		return view('admin.crud.view',$data);
	}
	/**
	 * This will load an insert form for current table
	 * @return view   load view page
	 */
	public function add()
	{
		$this->page_title = 'Add League';
		$data = $this->rendarAdd();
		$data['input_list']['sport_id']['field_label'] = 'Sport';
		$data['input_list']['country_id']['field_label'] = 'Country';
		return view('admin.crud.form',$data);
	}
	/**
	 * This will insert data into databse
	 * @param  LeagueRequest $request validated form request
	 * @return void                 redirect page
	 */
	public function insert(LeagueRequest $request)
	{
		$user_id = Session::get('user_id');
		$this->log('League Added From Admin',$user_id);

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
		$this->page_title = 'Edit League';
		$data = $this->rendarEdit($id);
		$this->changeFieldType('sport_id','select','Sport',null,null,'show-tick form-control','disabled');
		$this->changeFieldType('country_id','select','Country',null,null,'show-tick form-control','disabled');
		$this->changeFieldType('priority','number','Priority',null,null,'form-control','');
		$data = $this->rendarEdit($id);

		$sport_id = $data['input_list']['sport_id']['default_value'];
		$country_id = $data['input_list']['country_id']['default_value'];

		$data['input_list']['sport_id']['field_label'] = 'Sport';
		$data['input_list']['country_id']['field_label'] = 'Country';
		return view('admin.crud.form',$data);
	}
	/**
	 * this will update a row
	 * @param  LeagueRequest $request validated form request
	 * @return void                 redirect page
	 */
	public function update(LeagueRequest $request)
	{
		$league = League::find($request['id']);
		$sport_id = $league['sport_id'];
		$country_id = $league['country_id'];

		$request['sport_id'] = $sport_id;
		$request['country_id'] = $country_id;

		$user_id = Session::get('user_id');
		$this->log('League Updated From Admin',$user_id);

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
		$this->log('League Deleted From Admin',$user_id);

		$response = $this->deleteData($id);
		$this->forgetCache();
		return redirect($response);
	}

	/**
	 * removes all caches from the server
	 */
	public function forgetCache()
	{
		Cache::flush();
	}
	/**
	 * If you want to call any function for all, set here. by default crud will call this
	 * @return void        called by crud self
	 */
	public function callDefault()
	{
		// $this->setRelation('created_by', 'users', 'username');
		//$this->changeFieldType('role_id','muliselect','Role Name');
		//$this->addCallBackColoumn('field', 'label', 'callbackFunctionName');
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
