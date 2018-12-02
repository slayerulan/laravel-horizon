<?php
namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Frontend\FrontendBaseController;
use App\Http\Requests\SupportTicketRequest;
use App\Http\Traits\SupportTicket\TicketData;
use DB;
use App\User;
//use App\SupportTicket\SupportTicket;
use App\SupportTicket\StDepartment;
use App\SupportTicket\StMessage;
use App\SupportTicket\StPriority;
use App\SupportTicket\StStatusType;
use App\SupportTicket\StType;


/**
 *  this is the controller from where user profile settings will be managed,
 *  like edit profile, change password etc
 *
 *  @author Anirban Saha
 */
class SupportTicket extends FrontendBaseController
{
	use TicketData;
	/**
	 * Load profile edit form
	 * @return html load edit form
	 */
	public function index()
	{

	}

	/**
	 *  this will load support ticket page
	 *
	 *  @return  string  load html page
	 */
	public function getSupportTicket()
	{
	    $user_id = $this->user_id;

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
					->where('is_view', 'yes')
                	->get();

		$supportTicketsData = DB::table('support_tickets')
							->select(DB::raw('DISTINCT(support_tickets.id)'),'st_status_type_id','title','ticket_number','st_departments.name as deptName','st_types.name as typeName','st_priorities.name as priorityName','st_status_types.name as statusTypeName','support_tickets.created_at')
							->join('st_departments','support_tickets.st_department_id','=','st_departments.id')
							->join('st_types','support_tickets.st_type_id','=','st_types.id')
							->join('st_priorities','support_tickets.st_priority_id','=','st_priorities.id')
							->join('st_status_types','support_tickets.st_status_type_id','=','st_status_types.id')
                            ->where('support_tickets.user_id',$user_id)
							->whereNull('support_tickets.deleted_at')
							->orderBy('support_tickets.created_at','desc')
							->get();
		$stateNotSeenId = DB::table('support_tickets')
						->select('support_tickets.id')
						->join('st_messages','support_tickets.id','=','st_messages.ticket_id')
						->where('state','not seen')
						->get();
		$stateIds = array_map(function($o) { return $o->id; }, $stateNotSeenId->toArray());
		//P($stateIds);

		return view('frontend.supportTicket.support_ticket_list',['department' => $department,'ticketType' => $ticketType,  'priority' => $priority, 'statusType' => $statusType,'totalData' => $supportTicketsData,'notSeenIds'=>$stateIds]);
	}


	/**
	 *  this function is for load add support ticket page
	 */
	public function postSupportTicket(SupportTicketRequest $request)
	{
		if ($request->has('file')) {
			$type = explode('/', $_FILES['file']['type']);
			if ($type[0] == 'image' || $type[1] == 'pdf') {
				$this->supportTicketData($request);
				Session::flash('alert_class', 'success');
			    Session::flash('alert_msg', 'Successfully Added');
			}
			else{
				Session::flash("alert_class", "danger");
			    Session::flash("alert_msg", "Selected file type doesn't support");
			}
		}
		else{
			$this->supportTicketData($request);
			Session::flash('alert_class', 'success');
		    Session::flash('alert_msg', 'Successfully Added');
		}
		return redirect(route('front-get-support-ticket'));
	}


	/**
	 *  this function is for load ticket details
	 */
	public function getTicketDetails($id)
	{
		$ticket = DB::table('support_tickets')->where('ticket_number',$id)->get();

		$id = $ticket[0]->id;

		$data['ticket_details'] = $this->loadTicketDetails($ticket[0]);  //loadTicketDetails function declared in TicketData trait

		$st_messages = StMessage::where('ticket_id', $id)->get();
		$data['ticket_messages'] = $this->loadMessageDetails($st_messages); //loadMessageDetails function declared in TicketData trait
		$ticket_msg_data = array('state' => 'seen');

	    $update = StMessage::where('ticket_id', $id)
				->where('sender', '!=', $ticket[0]->user_id)
				->update($ticket_msg_data);

		return view('frontend.supportTicket.ticketDetails',$data);
	}


	/**
	 *  this function is for reply of ticket by player
	 */
	public function postTicketReply(Request $request)
	{
		//$this->supportTicketReply($request);
				$sender = $this->user_id;
        if($request->file('file'))
    		{
            $file = $request->file('file')->store('supportTicketFiles');
    		}
    		else
    		{
    			$file = '';
    		}
        $stMessage = new StMessage;
        $stMessage->ticket_id = $request->ticket_id;
        $stMessage->sender = $sender;
        $stMessage->message = $request->message;
        $stMessage->reply_file = $file;
        $stMessage->state = 'not seen';
        $stMessage->status = 'active';
        $stMessage->created_at = date("Y-m-d H:i:s");
        $stMessage->save();

        Session::flash('alert_class', 'success');
        Session::flash('alert_msg', 'Successfully Added');
				return redirect(route('front-get-support-ticket'));
		//return back();
	}

	/**
	 *  this function is for change of status by player
	 */

	public function postChangeStatus(Request $request)
	{
		$id = $request->id;
        $status_id = $request->status_id;
		DB::table('support_tickets')
            ->where('id', $id)
            ->update(['st_status_type_id' => $status_id]);
	 	return 1;
	}

    /**
	 *  this function is for check unread reply by player
	 */

     public function getUnreadSupportTticketReply()
     {
        $user_id = $this->user_id;
        $tickets = DB::table('support_tickets')->where('user_id',$user_id)->get();
        $unread = 0;

        if (!empty($tickets)) {
	    	foreach ($tickets as $ticket) {
                $supportTicketMsg = StMessage::where('ticket_id',$ticket->id)->where('sender','!=',$user_id)->where('state','not seen')->get();
                $not_viewed = count($supportTicketMsg);
			    if ($not_viewed != 0)
                {
			    	$unread = $unread + $not_viewed;
			    }
	    	}
	    }
        echo $unread;
     }

}
