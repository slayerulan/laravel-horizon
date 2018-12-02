
<div class="table-responsive">
			<table class=" crud-table table table-bordered table-striped table-hover dataTable js-exportable">
				<thead>
					<tr>
            <th>Start Time</th>
            <th>Sport</th>
            <th>Country</th>
						<th>League</th>
            <th>Home Team</th>
            <th>Away Team</th>
            <th>Result</th>
            <th>Status</th>
            <th>Action</th>
					</tr>
				</thead>
				<tbody>
          @forelse ($matches as $data)
              <tr>
                <td>{{ $data['time'] }}</td>
                <td>{{ $data['sport'] }}</td>
                <td>{{ $data['country'] }}</td>
                <td>{{ $data['league'] }}</td>
                <td>{{ $data['homeTeam'] }}</td>
                <td>{{ $data['awayTeam'] }}</td>
                <td>{{ $data['score'] }}</td>
                <td id="status_{{$data['id']}}">{{ $data['status'] }}</td>
                <td>
                    <select class="status_change" id="action_{{$data['id']}}" data-id="{{$data['id']}}">
                        <option value="active" <?php if($data['status'] == 'active'){echo 'selected';}?>>Active</option>
                        <option value="inactive" <?php if($data['status'] == 'inactive'){echo 'selected';}?>>Inactive</option>
                        <option value="cancel" <?php if($data['status'] == 'cancel'){echo 'selected';}?>>Cancel</option>
                    </select>
                </td>
              </tr>
          @empty
              <tr>
                  <td colspan="8">No match found!</td>
              </tr>
          @endforelse
				</tbody>
			</table>


			<div class="history_pagination_wrapper search_div_pagination">
        @if($total_page > 1)
        @php
          $left_check = 0;$right_check = 0;
        @endphp
        <ul id="history_pagination" class="blade-pagination" data-current="{{$active_page}}" data-total="{{$total_page}}">
          <li class="page first @if($active_page == 1) disabled @endif" data-page="1">First</li>
          <li class="page prev @if($active_page == 1) disabled @endif" data-page="{{(int)$active_page-1}}">Prev</li>
          @for($i=1;$i<=$total_page;$i++)
            @if($i > $active_page-5 && $i < $active_page+5 )
            <li class="page @if($i == $active_page) active disabled @endif" data-page="{{$i}}">{{$i}}</li>
            @elseif($i > 1 && $i < $active_page-5 && !$left_check)
              @php
              $left_check = 1;
              @endphp
              <li class="disabled"><span>...</span></li>
            @elseif($i > $active_page+5 && $i < $total_page  && !$right_check)
              @php
              $right_check = 1;
              @endphp
              <li class="disabled"><span>...</span></li>
            @endif

          @endfor
          <li class="page next @if($active_page == $total_page) disabled @endif" data-page="{{(int)$active_page+1}}">Next</li>
          <li class="page last  @if($active_page == $total_page) disabled @endif" data-page="{{$total_page}}">Last</li>
        </ul>
        @endif
      </div>
</div>