<?php

namespace App\Http\Controllers\admin\SupportTicket;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Crud;
use App\Http\Traits\SupportTicket\TicketData;
use App\Http\Requests\SupportTicketRequest;
use App\Http\Traits\DataSaver;
use DB;
use App\User;
use App\SupportTicket\SupportTicket;
use App\SupportTicket\StMessage;
use App\SupportTicket\StDepartment;
use App\SupportTicket\StPriority;
use App\SupportTicket\StStatusType;
use App\SupportTicket\StType;

/**
 *  This is a command to fetch all support tickets and save into database from admin panel.
 *
 *  @author	Sourav Chowdhury
 */
class MyTicketCrud extends Crud
{
	use TicketData;

	/**
	 * name of the table . REQUIRED
	 * @var string
	 */
	public $table_name 				= 'support_tickets';
	public $model_path = 'App\SupportTicket\\';
	/**
	 * route name that shold be used to create different action link. REQUIRED
	 * @var string
	 */
	public $route_slug 				= 'admin-support-ticket-management-all-tickets-';
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
	public $unset_actions_button	= ['edit'];

	public $unset_coloumn = ['id','created_at','updated_at','updated_by'];

	/**
	 * This will display table data in view page in data table
	 * @return view           	 load view page
	 */
    public function show()
    {
        $user_id = Session::get('user_id');
				$this->setLeftSideBarData();
				$add_button = route('admin-support-ticket-management-add-my-ticket');

				$ticket_status_type = StStatusType::where('status','active')->get()->toArray();

				$support_ticket = SupportTicket::where('allocate_to',$user_id)->get();

				$data = array();
				$allData = array();
				$totalData = array();
				$unread = 0;

				foreach($support_ticket as $support_ticket_data)
				{
					$player_id = $support_ticket_data->user_id;
					$users = checkParentIds($player_id); //checkParentIds defined in helpers
					$player = User::select('username')->where('id',$player_id)->get();

					$data['id'] = $support_ticket_data->id;
					$data['ticket_number'] = $support_ticket_data->ticket_number;
					$data['title'] = $support_ticket_data->title;
					$data['created_at'] = $support_ticket_data->created_at;
					$data['player'] = $player;
					$data['allocate_to'] = $support_ticket_data->allocate_to;
					$data['st_status_type_id'] = $support_ticket_data->st_status_type_id;
					$data['agents'] = $users;

					$StMessages = DB::table('st_messages')
								->where('st_messages.ticket_id',$support_ticket_data->id)
                ->where('st_messages.sender','!=',$user_id)
                ->where('support_tickets.allocate_to',$user_id)
                ->where('state','not seen')
          			->leftJoin('support_tickets', 'st_messages.ticket_id', '=', 'support_tickets.id')
          			->get();

					$unread = count($StMessages);
					$data['unread_msg'] = $unread;

					array_push($allData, $data);
				}
				$totalData['ticket_data'] = $allData;
				$totalData['ticket_status_type'] = $ticket_status_type;

				return view('admin/support-ticket/admin_support_ticket_my_list',['add_button' => $add_button,'support_ticket' => $totalData,'ticket_status_type' => $ticket_status_type,'profile_image' => $this->profile_image, 'parent_menu' => $this->parent_menu, 'sub_menu' =>$this->sub_menu]);
    }
	/**
	 * This will display a details for an id of this table
	 * @param  integer  $id      id of selected row
	 * @return view           	 load view page
	 */
	public function view($id)
	{
		$this->page_title = 'View Support Ticket';
		$data = $this->rendarView($id);
		return view('admin.crud.view',$data);
	}
	/**
	 * This will load an insert form for current table
	 * @return view   load view page
	 */
	public function add()
	{
		$this->page_title = 'Add Support Ticket';
		$data = $this->rendarAdd();
		return view('admin.crud.form',$data);
	}
	/**
     * this function is for load add support ticket page
     */
    public function getAddTicket()
    {
        $this->setLeftSideBarData();

		$department = StDepartment::where('status', 'active')
				   	->whereNull('deleted_at')
                   	->get();

		$ticketType = StType::where('status', 'active')
	   				->whereNull('deleted_at')
	               	->get();

	   	$priority = StPriority::where('status', 'active')
				   	->whereNull('deleted_at')
                   	->get();

		$statusType = StStatusType::where('status', 'active')
   				   	->whereNull('deleted_at')
                	->get();

        return view('admin/support-ticket/admin_add_my_ticket',['department' => $department, 'ticketType' => $ticketType,  'priority' => $priority, 'statusType' => $statusType,  'profile_image' => $this->profile_image, 'parent_menu' => $this->parent_menu, 'sub_menu' =>$this->sub_menu]);
    }
	/**
     * this function is for load add support ticket page
     */
    public function postAddTicket(SupportTicketRequest $request)
    {
		$this->supportTicketDataAdmin($request);

        Session::flash('alert_class', 'success');
        Session::flash('alert_msg', 'Successfully Added');
        return redirect(route('admin-support-ticket-management-my-tickets-list'));
    }
	/**
	 * this function is for load edit support ticket page
	 */
	public function getEditTicket($id)
	{
		$this->setLeftSideBarData();

		$ticket_data = DB::table('st_messages')
      ->where('support_tickets.id',$id)
			->groupBy('support_tickets.id')
			->leftJoin('support_tickets', 'st_messages.ticket_id', '=', 'support_tickets.id')
			->first();
		$player_id = $ticket_data->user_id;
		$users = checkParentIds($player_id); //checkParentIds defined in helpers
		$player = User::select('id', 'username')->where('id',$player_id)->get();

		$department = StDepartment::where('status', 'active')
				   	->whereNull('deleted_at')
                   	->get();

		$ticketType = StType::where('status', 'active')
	   				->whereNull('deleted_at')
	               	->get();

	   	$priority = StPriority::where('status', 'active')
				   	->whereNull('deleted_at')
                   	->get();

		$statusType = StStatusType::where('status', 'active')
   				   	->whereNull('deleted_at')
                	->get();

        return view('admin/support-ticket/admin_edit_my_ticket',['player' => $player, 'ticketData' => $ticket_data,'allUsers' => $users,'department' => $department, 'ticketType' => $ticketType,  'priority' => $priority, 'statusType' => $statusType,  'profile_image' => $this->profile_image, 'parent_menu' => $this->parent_menu, 'sub_menu' =>$this->sub_menu]);

	}
	/**
 	* this function is for insert support ticket data
 	*/
	public function postEditTicket(SupportTicketRequest $request)
	{
		$id = $request->id;
		$allocate_to = $request->allocate_to;
		$st_department_id = $request->st_department_id;
		$st_type_id = $request->st_type_id;
		$st_priority_id = $request->st_priority_id;
		$st_status_type_id = $request->st_status_type_id;

		$support_ticket = SupportTicket::find($id);

		if($request->file('file'))
        {
            $file = $request->file('file')->store('supportTicketFiles');
			if($support_ticket['file'])
			{
				Storage::delete($support_ticket['file']);
			}
        }
        else
		{
			if($support_ticket['file'])
			{
				$file = $support_ticket['file'];
			}
			else
			{
				$file = '';
			}
        }
		$support_ticket->allocate_to = $allocate_to;
		$support_ticket->st_department_id = $st_department_id;
		$support_ticket->st_department_id = $st_department_id;
		$support_ticket->st_type_id = $st_type_id;
		$support_ticket->st_priority_id = $st_priority_id;
		$support_ticket->st_status_type_id = $st_status_type_id;
		$support_ticket->file = $file;
		$support_ticket->updated_at = date("Y-m-d H:i:s");

		$support_ticket->save();

		$user_id = Session::get('user_id');
		$this->log('Support Ticket Updated From Admin',$user_id);

		Session::flash('alert_class', 'success');
        Session::flash('alert_msg', 'Successfully Updated');
        return redirect(route('admin-support-ticket-management-my-tickets-list'));

	}
	/**
     * this function is for post support ticket allocate to through ajax
     */
	public function postChangeAllocateTo(Request $request)
    {
        $id = $request->id;
        $allocate_to = $request->allocate_to;

        $support_ticket_update_data = array('allocate_to' => $allocate_to);
	    $update = SupportTicket::where('id', $id)->update($support_ticket_update_data);
		if($update)
		{
			echo 1;
		}
		else {
			echo 0;
		}
    }
	/**
     * this function is for post support ticket status through ajax
     */
	public function postChangeStatus(Request $request)
    {
        $id = $request->id;
        $status_id = $request->status_id;

		$support_ticket = SupportTicket::find($id);

		$support_ticket->st_status_type_id = $status_id;
		$support_ticket->save();
	 	return 1;
    }
	/**
	 * This will insert data into databse
	 * @param  SupportTicketRequest $request validated form request
	 * @return void                 redirect page
	 */
	public function insert(SupportTicketRequest $request)
	{
		$user_id = Session::get('user_id');
		$this->log('Support Ticket Added From Admin',$user_id);

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
		$this->page_title = 'Edit SupportTicket';
		$data = $this->rendarEdit($id);
		return view('admin.crud.form',$data);
	}
	/**
	 * this will update a row
	 * @param  SupportTicketRequest $request validated form request
	 * @return void                 redirect page
	 */
	public function update(SupportTicketRequest $request)
	{
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
		$this->log('Support Ticket Deleted From Admin',$user_id);

		$support_ticket = SupportTicket::find($id);

		if($support_ticket->file)
		{
			Storage::delete($support_ticket->file);
		}

		$response = $this->deleteData($id);

		$ticket_msg_data = array('deleted_at' => date("Y-m-d H:i:s"));
	    $update = StMessage::where('ticket_id', $id)->update($ticket_msg_data);
        Session::flash('alert_class', 'success');
        Session::flash('alert_msg', 'successfully deleted');
        return redirect(route('admin-support-ticket-management-my-tickets-list'));
		//return redirect($response);
	}
	/**
     * this function is for show support ticket messages through ajax
     */
	public function postShowMessage(Request $request)
    {
		$id = $request->id;
		$ticket = SupportTicket::find($id);

		$data['ticket_details'] = $this->loadTicketDetails($ticket);  //loadTicketDetails function declared in TicketData trait
		$st_messages = StMessage::where('ticket_id', $id)->get();
		$data['ticket_messages'] = $this->loadMessageDetails($st_messages); //loadMessageDetails function declared in TicketData trait
		$ticket_msg_data = array('state' => 'seen');
	    $update = StMessage::where('ticket_id', $id)
				->where('sender', '!=', Session::get('user_id'))
				->update($ticket_msg_data);

		return view('admin.support-ticket.st_messages',$data);
	}

	/**
     * this function is for post support ticket reply
     */
	public function postTicketReply(Request $request)
    {
		$this->supportTicketReply($request);

        Session::flash('alert_class', 'success');
        Session::flash('alert_msg', 'Successfully Added');
        return redirect(route('admin-support-ticket-management-all-tickets-list'));
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
		$where = "WHERE support_tickets.deleted_at is null";
		$this->additional_where   = $where;
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
