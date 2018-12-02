<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use Illuminate\Support\Facades\Storage;
use App\Http\Traits\Rbac;
use App\Http\Library\Crud\Traits\ShowList;
use App\Http\Library\Crud\Traits\ModifyData;
use App\Http\Library\Crud\Traits\Form;
use App\Http\Library\Crud\Traits\DynamicFieldGenerator;

/**
 *  It is the base crud controller. All crud will inherit This
 *
 *  @author Anirban Saha
 */
class Crud extends Controller
{
	/**
	 * we will use roll base access controll
	 */
    use Rbac,DynamicFieldGenerator,ShowList,Form,ModifyData;
	/**
	 * model name of the specific crud
	 */
	public $model_name;
	/**
	 * table name of the specific crud
	 */
	public $table_name;
	/**
	 * name of modulr if there is RBAC
	 */
	public $module_slug				= '';
	/**
	 * what will be the basic route for specific group
	 */
	public $route_slug;
	/**
	 * page title visible in top
	 */
	public $page_title				= '';
	/**
	 * If you want to use rbac menu
	 * @var bool
	 */
	public $use_rbac				= false;
	/**
	 * where to upload any files
	 * @var string
	 */
	public $upload_path				= false;
	/**
	 * contain table data of current table
	 * @var array
	 */
	private $show_table_data 		= array();
	/**
	 * contain table fields name and their label
	 * @var array
	 */
	private $show_table_coloumns 	= array();
	/**
	 * Specify current action
	 * @var string
	 */
	private $action_type 			= false;
	/**
	 * This will contain all form list
	 * @var array
	 */
	private $form_input_list 		= false;
	/**
	 * This will return all data for show page
	 * @return array all required data including menu varriable
	 */
	protected function rendarShow()
	{
		$this->action_type = 'list';
        $this->setAdd();
		$data = $this->rendarDefault();
		$this->setShowTableData();
		$data['page_title'] 	= $this->page_title;
		$data['show_export'] 	= $this->show_export;
		$data['table_data'] 	= $this->show_table_data;
		$data['table_field'] 	= $this->show_table_coloumns;
		$data['add_button'] 	= $this->add_button;
		return  $data;
	}
	/**
	 * This will return all required data for view page
	 * @param  integer $id id of this table
	 * @return array     required data for view page
	 */
	protected function rendarView($id)
	{
		$this->action_type 			= 'view';
		$id 						= cleanData($id);
		$this->unset_actions_button = ['edit','view','delete'];
		$data 						= $this->rendarDefault();
		$condition					= str_contains($this->additional_where, 'where') ? ' AND ' : ' WHERE ';
		$this->additional_where		=	$condition.$this->table_name.'.id ='.$id;
		$this->setShowTableData();
		if(isset($this->show_table_data[0])){
			$data['details'] 		= $this->show_table_data[0];
			$data['label_data'] 	= $this->show_table_coloumns;
			$data['back_url'] 		= route($this->route_slug.'list');
			return  $data;
		}else{
			abort(404, 'No data found');
		}
	}
	/**
	 * This will rendar insert form
	 * @return array required data for insert form
	 */
	protected function rendarAdd()
	{
		$this->action_type 			= 'add';
		$data 						= $this->rendarDefault();
		$this->setInsertForm();
		if(is_array($this->form_input_list)){
			$data['input_list'] 	= $this->form_input_list;
			$data['insert_url'] 	= route($this->route_slug.'insert');
			return  $data;
		}else{
			abort(404, 'No data found');
		}
	}
	/**
	 * This will insert data into database
	 * @param  object $request form reequest object
	 * @return string          return url
	 */
	protected function insertData($request)
	{
		$this->action_type 			= 'insert';
		$this->setUploadPath();
		if($this->insertDB($request)){
			Session::flash('alert_class', 'success');
			Session::flash('alert_msg', 'successfully added');
			$url = route($this->route_slug.'list');
		}else {
			Session::flash('alert_class', 'danger');
			Session::flash('alert_msg', 'error occured');
			$url = route($this->route_slug.'add');
		}
		return $url;
	}
	/**
	 * This will update a row
	 * @param  object $request form request object
	 * @return string          return url
	 */
	protected function updateData($request)
	{
		$this->action_type 		= 'update';
		$this->setUploadPath();
		if($this->updateDB($request)){
			Session::flash('alert_class', 'success');
			Session::flash('alert_msg', 'successfully updated');
			$url = route($this->route_slug.'list');
		}else {
			Session::flash('alert_class', 'danger');
			Session::flash('alert_msg', 'error occured');
			$url = route($this->route_slug.'edit/'.$request->id);
		}
		return $url;
	}
	/**
	 * This will render an edit form
	 * @param  integer $id id of row
	 * @return array     required data for edit form
	 */
	public function rendarEdit($id)
	{
		$this->action_type 		= 'edit';
		$id 					= cleanData($id);
		$data 					= $this->rendarDefault();
		$condition				= str_contains($this->additional_where, 'where') ? ' AND ' : ' WHERE ';
		$this->additional_where		=	$condition.$this->table_name.'.id ='.$id;
		$this->setEditForm();
		if(is_array($this->form_input_list)){
			$data['input_list'] = $this->form_input_list;
			$data['insert_url'] = route($this->route_slug.'update');
			return  $data;
		}else{
			abort(404, 'No data found');
		}
	}
	/**
	 * This will delete a row
	 * @param  integer $id id of row
	 * @return string	     return url
	 */
	public function deleteData($id)
	{
		$this->action_type 		= 'delete';
		$id 					= cleanData($id);
		$this->deleteDB($id);
		Session::flash('alert_class', 'success');
		Session::flash('alert_msg', 'successfully deleted');
		$url = route($this->route_slug.'list');
		return $url;
	}
	/**
	 * This will call some default function for rendaring
	 * @return array return general data
	 */
	public function rendarDefault()
	{
		if($this->use_rbac){
			if(!$this->canModify( $this->module_slug)){
				if (($key = array_search('edit',$this->actions_button)) !== FALSE) {
				  unset($this->actions_button['edit']);
				}
				if (($key = array_search('delete',$this->actions_button)) !== FALSE) {
				  unset($this->actions_button['delete']);
				}
			}
			if(!$this->canAdd( $this->module_slug)){
				$this->unsetAdd();
			}
		}
		$this->setUploadPath();
		$this->setLeftSideBarData();
		$data['parent_menu'] 	= $this->parent_menu;
		$data['sub_menu'] 		= $this->sub_menu;
		$data['page_title'] 	= $this->page_title;
		$data['profile_image'] 	= $this->profile_image;
		$this->callDefault();
		return $data;
	}
	/**
	 * Default callback function for image coloumn
	 * @param object $row_data current row object
	 */
	public function setImage($row_data,$value,$type)
	{
		if($type =="list" || $type =="view"){
			$file_path = asset('storage/'.$value);
			return '<img src="'.$file_path.'" width="100" alt="image-thumb"/>';
		}
		return $value;
	}
	/**
	 * This is default callback function for status field
	 * @param object $row_data current row object
	 * @param string $value    current field value
	 * @param string $type     current action type
	 * @return string $value   final value
	 */
	public function setStatus($row_data,$value,$type)
	{
		if($type =="list" || $type =="view"){
			$status_type = ['1'=>'Active','0'=>'Inactive'];
			$status	= isset($status_type[$row_data->status]) ? $status_type[$row_data->status] : $row_data->status;
			$type = ['active'=> 'bg-green', 'inactive' => 'bg-red','1'=>'bg-green','0'=>'bg-red'];
			$value = isset($type[$value]) ? $type[$value] : 'bg-grey';
			return '<span class="badge '.$value.'"> '.$status.' </span>';
		}
		return $value;
	}
	/**
	 * This will set default upload path
	 */
	private function setUploadPath()
	{
		$this->upload_path = $this->upload_path ? $this->upload_path : $this->table_name;
		if(!is_dir($this->upload_path)){
			Storage::makeDirectory($this->upload_path);
		}
	}
	/**
	 * should override this method in crud controller
	 * @return void        called by crud self
	 */
	public function callDefault()
	{

	}
}
