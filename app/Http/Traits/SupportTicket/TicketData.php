<?php
/**
 * This will contain all permission formula
 */

namespace App\Http\Traits\SupportTicket;
use Illuminate\Http\Request;
use Session;
use DB;
use App\User;
use App\SupportTicket\SupportTicket;
use App\SupportTicket\StMessage;
use App\SupportTicket\StDepartment;
use App\SupportTicket\StPriority;
use App\SupportTicket\StStatusType;
use App\SupportTicket\StType;
use Mail;
use App\Mail\SupportTicket\TicketMail;
use App\Notifications\NotifyTicketUser;
use App\Notification;

/**
 *  This is for insert/update player & agent data into database from admin panel.
 *
 *  @author	Sourav Chowdhury
 */
trait TicketData{
  /**
   * this function is for insert ticket details into table from front-end
   */
    public function supportTicketData( $request)
    {
        $user = $this->user;
        $user_id = $this->user_id;
        if (isset($this->agent)) {
            $allocate_to = $this->agent->id;
        }
        else{
            $allocate_to = NULL;
        }
        $sender = $user_id;
        $ticket_number = $request->ticket_number;
        $st_department_id = $request->st_department_id;
        $st_type_id = $request->st_type_id;
        $st_priority_id = $request->st_priority_id;
        $st_status_type_id = $request->st_status_type_id;
        $title = $request->title;
        $message = $request->message;

        if($request->file('file'))
        {
            $file = $request->file('file')->store('supportTicketFiles');
        }
        else {
            $file = '';
        }

        $chars = "0123456789";
		    $ticket_number = substr( str_shuffle( $chars ), 0, 12 );

        $support_ticket_data = array('user_id'=>$user_id,
                           'title'=>$title,
                           'allocate_to'=>$allocate_to,
                           'ticket_number'=>$ticket_number,
                           'st_department_id' => $st_department_id,
                           'st_type_id' => $st_type_id,
                           'st_priority_id' => $st_priority_id,
                           'st_status_type_id' => $st_status_type_id,
                           'file' => $file,
                           'created_at' => date("Y-m-d H:i:s"));
                $ticket_id = DB::table('support_tickets')
                ->insertGetId($support_ticket_data);

                $this->log('New Support Ticket Added From Admin');

        $st_messages_data = array('ticket_id'=>$ticket_id,
                             'sender'=>$sender,
                             'message'=>$message,
                             'state'=>'not seen',
                             'status'=>'active',
                             'created_at' => date("Y-m-d H:i:s"));
                $st_messages_id = DB::table('st_messages')
                ->insertGetId($st_messages_data);
        if($st_messages_data)
        {
            $user_data = User::where('id',$user_id)->get()->toArray();
            $full_name = $user_data[0]['full_name'];

            $to_email = $user_data[0]['email'];
            $this->full_name = $full_name;

            Mail::to($to_email)->send(new TicketMail($this));

            $ticket = SupportTicket::find($ticket_id);
            // User::find($allocate_to)->notify(new NotifyTicketUser($ticket));
        }
    }
    /**
     * this function is for insert ticket details into table from admin
     */
    public function supportTicketDataAdmin( $request)
    {
        $sender = Session::get('user_id');

        $user_id = $request->user_id;
        $allocate_to = $request->allocate_to;
        $ticket_number = $request->ticket_number;
        $st_department_id = $request->st_department_id;
        $st_type_id = $request->st_type_id;
        $st_priority_id = $request->st_priority_id;
        $st_status_type_id = $request->st_status_type_id;
        $title = $request->title;
        $message = $request->message;
        $user_id = $request->user_id;

        if($request->file('file'))
        {
            $file = $request->file('file')->store('supportTicketFiles');
        }
        else {
            $file = '';
        }

        $chars = "0123456789";
		    $ticket_number = substr( str_shuffle( $chars ), 0, 12 );

        $support_ticket_data = array('user_id'=>$user_id,
                           'title'=>$title,
                           'allocate_to'=>$allocate_to,
                           'ticket_number'=>$ticket_number,
                           'st_department_id' => $st_department_id,
                           'st_type_id' => $st_type_id,
                           'st_priority_id' => $st_priority_id,
                           'st_status_type_id' => $st_status_type_id,
                           'file' => $file,
                           'created_at' => date("Y-m-d H:i:s"));
                $ticket_id = DB::table('support_tickets')
                ->insertGetId($support_ticket_data);

                $this->log('New Support Ticket Added From Admin');

        $st_messages_data = array('ticket_id'=>$ticket_id,
                             'sender'=>$sender,
                             'message'=>$message,
                             'state'=>'not seen',
                             'status'=>'active',
                             'created_at' => date("Y-m-d H:i:s"));
                $st_messages_id = DB::table('st_messages')
                ->insertGetId($st_messages_data);
        if($st_messages_data)
        {
            $user_data = User::where('id',$user_id)->get()->toArray();
            $full_name = $user_data[0]['full_name'];

            $to_email = $user_data[0]['email'];
            $this->full_name = $full_name;

            Mail::to($to_email)->send(new TicketMail($this));
        }
    }

    /**
     * this function is for get support ticket details
     */
	function loadTicketDetails($ticket)
	{
		if (!empty($ticket)) {
			   $ticket_details[] = [
				   'id' => $ticket->id,
				   'title' => $ticket->title,
				   'ticket_number' => $ticket->ticket_number,
				   'date' => $ticket->created_at,
				   'st_department' => StDepartment::find($ticket->st_department_id),
				   'st_priority' => StPriority::find($ticket->st_priority_id),
				   'st_type' => StType::find($ticket->st_type_id),
                   'st_status_type' => StStatusType::find($ticket->st_status_type_id),
				   'file' => $ticket->file,
			   ];
		   return $ticket_details;
	   }
	}
	/**
     * this function is for get support ticket messages
     */
    function loadMessageDetails($messages) {
		foreach ($messages as $message) {
			$sender = User::where('id', $message->sender)->get();
	        $ticket_messages[] = [
	        	'message' => $message,
	        	'sender' => $sender,
	        ];
		}
		return $ticket_messages;
	}

    public function supportTicketReply( $request)
    {
        $sender = Session::get('user_id');
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
    }

}
