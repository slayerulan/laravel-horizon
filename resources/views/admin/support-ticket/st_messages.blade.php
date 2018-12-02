<div class="">
	<table class="table table-striped newTickets">
		<tbody>
            <tr>
                <td>Title</td>
                <td><?=$ticket_details[0]['title']?></td>
            </tr>
			<tr>
			   <td>Ticket Number</td>
                <td><?=$ticket_details[0]['ticket_number']?></td>
			</tr>
			<tr>
			   <td>Department</td>
                <td><?=$ticket_details[0]['st_department']['name']?></td>
			</tr>
			<tr>
			   <td>Priority</td>
				<td><?=$ticket_details[0]['st_priority']['name']?></td>
			</tr>
			<tr>
			   <td>Type</td>
				<td><?=$ticket_details[0]['st_type']['name']?></td>
			</tr>
			<tr>
			   <td>Status</td>
				<td><?=$ticket_details[0]['st_status_type']['name']?></td>
			</tr>

			<?php if($ticket_details[0]['file']) { ?>
			<tr>
			   <td>File</td>
				<td><a target="_blank" href="{{ asset( 'storage/'.$ticket_details[0]['file'] ) }}">Click here</a></td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
	<div class="messags_show_wrap">
		<div class="message_show">
			<?php foreach ($ticket_messages as $message) { ?>
				<div class="<?php if($message['sender'][0]->role_id == 1){echo 'message_right';}else{echo 'message_left';} ?>">
					<h5>
						<?=$message['sender'][0]->full_name?>
						<?php if ($message['message']->state == 'seen' && $message['sender'][0]->role_id != 1) { ?>
							<span class="ReadActive"><i class="fa fa-check-square-o"></i></span>
						<?php } ?>
					</h5>
					<span><?=$message['message']->created_at?></span>
					<p class="comment_text"><?=$message['message']->message?></p>
					<?php if($message['message']->reply_file) { ?>
						<p class="comment_text"><a target="_blank" href="{{ asset( 'storage/'.$message['message']->reply_file ) }}">Click here</a></p>
					<?php } ?>
				</div>
			<?php } ?>
		</div>
	</div>

	<?php if ($ticket_details[0]['st_status_type']['id'] != 2) { ?>
		<div class="col-md-8 col-md-offset-2 reply_ticket">
			<h3>Reply</h3>
			<form method="post" action="{{route('admin-support-ticket-management-ticket-reply')}}" id="support_ticket_reply" name="support_ticket_reply" enctype="multipart/form-data">
				<input type="hidden" name="_token" value="{{ csrf_token() }}"/>
				<input type="hidden" name="ticket_id" value="<?=$ticket_details[0]['id']?>">
				<div class="form-group">
					<label for="content"> Message </label>
					<textarea id="message" class="validate[required]" name="message" value="Write your reply...."></textarea>
					<div id="reply_val"></div>

					<label class="form-label">File</label>
                    <div class="image">
                        <input name="file" type="file" value="{{ old('file') }}"/>
                    </div>
				</div>


				<div class="form-group">
					<button type="submit" class="btn btn-success" id="st_reply_submit"><i class="fa fa-location-arrow"></i> Reply</button>
				</div>


			</form>
		</div>
	<?php } ?>
	<div class="clearfix"></div>
</div>

<script type="text/javascript">
	$(document.body).on('submit','#support_ticket_reply',function(){
        var message = $('#message').val();
        if (message == '') {
            $('#reply_val').html('<I style="color:red;">Write your reply</I>');
            setTimeout(function(){ $('#reply_val').html(''); }, 4000);
        }
        if (message == '') {
        	return false;
        }
        else{
            return true;
        }
	});
</script>
