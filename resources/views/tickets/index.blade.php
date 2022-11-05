 @extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">


      <div class="row">
				<div class="col-lg-12">
					<h1 class="page-header">{{__('Tickets List')}}</h1>
				</div>
				<!-- /.col-lg-12 -->
			</div>


      @include('layouts.flash')


      <div class="row" >
        <div class="col-md-2">
          <input id="textRangeFrom" placeholder="Date From" type="text" class="form-control" value=""/><span class="calendarIcon">
        </div>
        <div class="col-md-2">
          <input id="textRangeTo" placeholder="Date To" type="text"  class="form-control" value=""/><span class="calendarIcon">
        </div>
        <div class="col-md-2">
          <button id="dateRangeBtn" class="btn btn-primary">Go</button>
        </div>
      </div>
      <br/>
      <div class="row">
        <div class="col-lg-3">
          <select id="hourFilter" class="form-control">
                <option value="">%Time of remaining hours</option>
                <option value="50">50%</option>
                <option value="25">25%</option>
                <option value="10">10%</option>
                <option value="0">0%</option>
                <option value="-1">Overdue</option>

          </select>
        </div>

        <div class="col-lg-3">
          <select id="siteFilter" class="form-control">
              <option value="0">Site Filter</option>
              @if(count($sites)>0)
                @foreach($sites as $v)
                  <option value="{{$v->id}}">{{$v->name}}</option>
                @endforeach
              @endif
          </select>
        </div>

        <div class="col-lg-3">
          <select id="statusFilter" class="form-control">
              <option value="0">Status Filter</option>
                  <option value="open">Open</option>
                  <option value="new">New</option>
                  <option value="close">Close</option>
                  <option value="reopen">Re-open</option>
          </select>
        </div>
      </div>

      <br/>
      <table id="tableData" class="table-responsive table table-striped table-bordered" style="font-size:12px;width:100% !important">

          <thead>
              <tr>
                  <th >Ticket Id</th>
                  <th >Created</th>
                  <th>Subject</th>
                  <th>Issue Type</th>
                  <th>Category</th>
                  <th>Site</th>
                  <th>Equipment</th>
                  {{-- <th>Stretch</th> --}}
                  <th>Priority</th>
                  <th>Remaining Hours</th>
                  <th>Status</th>
                  <th>Details</th>
              </tr>
          </thead>
          <tbody>

          </tbody>
          <tfoot>
              <tr>
                <th >Ticket Id</th>
                <th >Created</th>
                  <th>Subject</th>
                  <th>Issue Type</th>
                  <th>Category</th>
                  <th>Site</th>
                  <th>Equipment</th>
                  {{-- <th>Stretch</th> --}}
                  <th>Priority</th>
                  <th>Remaining Hours</th>
                  <th>Status</th>
                  <th>Details</th>
              </tr>
          </tfoot>
      </table>


		</div>
	</div>





  <div class="modal fade" id="equipmentModal" tabindex="-1" role="dialog" aria-labelledby="equipmentModal" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="equipmentModalLabel">Ticket Details</h4>
      </div>

      {!! Form::open(array('route' => 'tickets.index','method'=>'POST','id'=>'add_ticket_comment_form')) !!}
          <input type="hidden" id="hidden_ticket_id" name="hidden_ticket_id" value="">
          <div class="modal-body">

              <div class="col-xs-12 col-sm-12 col-md-12" id="ticket_comment_div">
                 &nbsp;
              </div>
              <div class="col-xs-12 col-sm-12 col-md-12" id="reason_div" style="display: none">
                <div class="form-group">
                    <strong>Closing By</strong>
                    <select name="closing" id="reason_close" class="form-control">

                    </select>
                </div>
            </div>
          
              <div class="col-xs-12 col-sm-12 col-md-12">
                  <div class="form-group">
                      <strong>Comments</strong>
                      {!! Form::textarea('comment', '', array('id'=>'commentbox','rows'=>5,'required'=>'true','class' => 'form-control','placeholder'=>'Add Comment')) !!}
                  </div>
              </div>
           </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
        {!! Form::close() !!}
    </div>
  </div>
</div>



  <script>

    var url = "{{url('/')}}";
    var table = '';
    var seconds = 0;

    jQuery(document).ready(function() {


          table = jQuery('#tableData').DataTable({
            'processing': true,
            'serverSide': true,
            'lengthMenu': [
              [10, 25, 50, -1], [10, 25, 50, "All"]
            ],
            dom: 'Bfrtip',
            buttons: [
            {extend:'csvHtml5',
              exportOptions: {
                columns: [0, 1,3,4,5,6,7,8]//"thead th:not(.noExport)"
              },
              className: 'btn btn-default',
                init: function(api, node, config) {
                  $(node).removeClass('dt-button')
                },
            },
            {extend: 'pdfHtml5',
              exportOptions: {
                columns: [0, 1,3,4,5,6,7,8] //"thead th:not(.noExport)"
              },
              filename: function() {
                return 'ticket-list'
              },
              title: function() {
                return $('#test123').val()
              },
              className: 'btn btn-default',
                init: function(api, node, config) {
                  $(node).removeClass('dt-button')
                },
              customize : function(doc){
                    var colCount = new Array();
                    var length = $('#reports_show tbody tr:first-child td').length;
                    //console.log('length / number of td in report one record = '+length);
                    $('#reports_show').find('tbody tr:first-child td').each(function(){
                        if($(this).attr('colspan')){
                            for(var i=1;i<=$(this).attr('colspan');$i++){
                                colCount.push('*');
                            }
                        }else{ colCount.push(parseFloat(100 / length)+'%'); }
                    });
              }
            },
            {
            extend:'pageLength',
            className: 'btn btn-default',
                init: function(api, node, config) {
                  $(node).removeClass('dt-button')
                },

            }
            ],
            'sPaginationType': "simple_numbers",
            'searching': true,
            "bSort": true,
            "fnDrawCallback": function (oSettings) {

            },
            'fnRowCallback': function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
              //if (aData["status"] == "1") {
                //jQuery('td', nRow).css('background-color', '#6fdc6f');
              //} else if (aData["status"] == "0") {
                //jQuery('td', nRow).css('background-color', '#ff7f7f');
              //}
              //jQuery('.popoverData').popover();
            },
            "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            //   console.log(aData.hours_consumed);
            //   rgb(239 134 95) close with overdue
            //   red open with overdue
              if (aData.status == "close") {
                  if(aData.close_time > "0:0")
                  {
                      // console.log('1');
                      $('td', nRow).css('background-color', '#90ee90');
                  }
                  else{
                    // console.log('0');
                    $('td', nRow).css('background-color', 'rgb(239 134 95)');

                  }

              } else if (aData.status == "reopen") {
                $('td', nRow).css('background-color', '#fed8b1');
              } else if (aData.status == "fixed") {
                $('td', nRow).css('background-color', '#ebecf0');
              }
              else if(aData.status == "Closure Request")
              {
                $('td', nRow).css('background-color', 'rgb(144 238 144 / 65%)');
              }
              else if(aData.status == "open" && aData.hours_consumed < "0:0")
              {
                $('td', nRow).css('background-color', 'red');
              }
            },
						"initComplete": function(settings, json) {
              //jQuery('.popoverData').popover();
					  },
            'ajax': {
              'url': '{{ url("/") }}/tickets/ajaxData',
              'headers': {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
              },
              'type': 'post',
              'data': function(d) {
                d.date_from = jQuery('#textRangeFrom').val();
                d.date_to = jQuery('#textRangeTo').val();
                d.siteFilter = jQuery("#siteFilter option:selected").val();
                d.statusFilter = jQuery("#statusFilter option:selected").val();
                d.hourFilter = jQuery("#hourFilter option:selected").val();
              },
            },

            'columns': [
                {
                  'data': 'TicketID',
                  'className': 'col-md-2',
                  'render': function(data,type,row){

                    return row.ticket_id;
                  }
              },
                {
                  'data': 'Created',
                  'className': 'col-md-2',
                  'render': function(data,type,row){

                    return row.created_at;
                  }
              },

              {
                  'data': 'subject',
                  'className': 'col-md-2',
                  'render': function(data,type,row){

                    return row.subject;
                  }
              },
              {
                  'data': 'issue_type_name',
                  'className': 'col-md-1',
                  'render': function(data,type,row){

                    return row.issue_type_name;
                  }
              },
              {
                  'data': 'ticket_category_name',
                  'className': 'col-md-1',
                  'render': function(data,type,row){

                    return row.ticket_category_name;
                  }
              },
              {
                  'data': 'site_name',
                  'className': 'col-md-1',
                  'render': function(data,type,row){

                    return row.site_name;
                  }
              },
              {
                  'data': 'equipment_title',
                  'className': 'col-md-1',
                  'render': function(data,type,row){

                    return row.equipment_title;
                  }
              },
            //   {
            //       'data': 'stretch_point',
            //       'className': 'col-md-1',
            //       'render': function(data,type,row){

            //         return row.stretch_point;
            //       }
            //   },
              {
                  'data': 'priority',
                  'className': 'col-md-1',
                  'render': function(data,type,row){

                    return row.priority;
                  }
              },
              {
                  'data': 'hours_consumed',
                  'className': 'col-md-1',
                  'orderable': false,
                  'render': function(data,type,row){
                      if(row.status == 'close')
                      {
                        return row.close_time;
                      }
                      else{
                        return row.hours_consumed;
                      }


                  }
              },
              {
                  'data': 'status',
                  'className': 'col-md-1',
                  'render': function(data,type,row){
                    if(row.status == 'close')
                    {
                      if(row.close_time > "0:0")
                      {
                        return row.status;
                      }
                      else{
                        return 'Overdue Closed Ticket';
                      }
                    }
                    return row.status;
                  }
              },
              {
                'data': 'Details',
                'orderable': false,
                'className': 'col-md-1',
                'render': function(data, type, row) {
                  var buttonHtml = '<button class="btn btn-info ticket_comment_btn" onclick="getTicketInfo('+row.id+')">Details</button>';
                  return buttonHtml;
                }
              }
            ]
          });

          $('#textRangeFrom').datePicker(
            {
              monthCount: 3,
              range: '#textRangeTo',
            }
          );

          $('#dateRangeBtn').on('click',function(){
            table.draw();
          });

          $('#siteFilter').change(function () {
            table.draw();
          });
          $('#hourFilter').change(function () {
            table.draw();
          });
          $('#statusFilter').change(function () {
            table.draw();
          });



      $('#add_ticket_comment_form').submit(function(e){
        e.preventDefault();

        $.ajax({
            type: "POST",
            url: "{{url('/')}}/tickets/addcomments",
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: $('#add_ticket_comment_form').serialize(),
            success: function(res) {
                if(res.status){
                  $('#equipmentModal').modal('hide');
                  location.reload();
                }else{
                  jQuery.alert({
                      title: 'Alert!',
                      content: res.message,
                  });
                }

                //$('#vehicleModal').modal('show');

            },
            error:function(request, status, error) {
                console.log("ajax call went wrong:" + request.responseText);
            }
        });
      });


});

function getTicketInfo(id){
    var url = "{{url('/')}}/tickets/getTicketInfo";
    var ticket_id = id;

    if(ticket_id==""){

      jQuery.alert({
        title: 'Alert!',
        content: 'Ticket is is not mentioned!',
      });

      return false;
    }
    $('#hidden_ticket_id').val(ticket_id);
    $.ajax({
        type: "POST",
        url: url,
        data: {
          "_token": $('meta[name="csrf-token"]').attr('content'),
          "ticket_id": ticket_id
        },
        success: function(res) {
            if(res.status){

              var statusbox = '<select name="status" class="form-control" onchange="reson_for(this)" id="ticketstatus">';
              var selected = '';
              var statusArr = ['open','fixed','close'];
              if(res.data.ticket[0].status=='close'){
                statusArr.push('reopen');
                statusArr.splice(0, 2);

              }
              jQuery.each(statusArr, function(index, item) {
                if(res.data.ticket[0].status==item){
                  selected = 'selected';
                }
                statusbox += '<option value="'+item+'" '+selected+'>'+item+'</option>';
                selected = '';
              })
              statusbox += '</select>';


              var selectbox = '<select name="siteuser" class="form-control">';
              var selected = '';
              jQuery.each(res.data.siteuser, function(index, item) {
                if(res.data.ticket[0].assign_to==item.id){
                  selected = 'selected';
                }
                selectbox += '<option value="'+item.id+'" '+selected+'>'+item.name+'('+item.email+')</option>';
                selected = '';
              })
              selectbox += '</select>';
              var html = '';
              var html_closuer = '';
              if(res.data.closure_request.length>0)
              {
                html_closuer += '<div class="alert alert-danger"> ';
                    html_closuer += '<div class="card-body">';
                      html_closuer += '<h6 class="card-title"><b>'+res.data.closure_request[0].username+'</b></h6>';
                      html_closuer += '<p class="card-text">'+res.data.closure_request[0].description+'</p>';
                      html_closuer += '<h6>'+res.data.closure_request[0].updated_at+'</h6>';


                    html_closuer += '</div> ';
                  html_closuer += '</div> ';
              }
              jQuery.each(res.data.comments, function(index, item) {
			
                  html += '<div class="alert alert-success"> ';
                    html += '<div class="card-body">';
                      html += '<h6 class="card-title"><b>'+item.username+'</b></h6>';
                      html += '<p class="card-text">'+item.comment+'</p>';
                      html += '<h6>'+item.created_at+'</h6>';
                    //   console.log(item.image);
                      if(item.image != null)
                      {
                        html += '<img width="25%" src="'+item.image+'">';
                      }

                    html += '</div> ';
                  html += '</div> ';

              });
		
              var str = '<table id="tableData" class="table-responsive table table-striped table-bordered">';
                if(res.data.ticket[0].status == 'close')
                {
                    str += '<tr><th>Subject:</th><td>'+res.data.ticket[0].subject+'<span class="ticket_timer1" style="float:right">'+res.data.ticket[0].close_time+'</span><input type="hidden" name="closeTime" value="'+res.data.ticket[0].remaining_hours+'"></td></tr>';

                }
                else{
                str += '<tr><th>Subject:</th><td>'+res.data.ticket[0].subject+'<span class="ticket_timer" style="float:right"></span><input type="hidden" name="closeTime" value="'+res.data.ticket[0].remaining_hours+'"></td></tr>';

                }
                str += '<tr><th>Description</th><td>'+res.data.ticket[0].description+'</td></tr>';
                str += '<tr><th>Issue Type</th><td>'+res.data.ticket[0].issue_type_name+'</td></tr>';
                str += '<tr><th>Equipment </th><td>'+res.data.ticket[0].equipment_title+'</td></tr>';
                str += '<tr><th>Stretch Point </th><td>'+res.data.ticket[0].stretch_point+'</td></tr>';
                str += '<tr><th>Priority</th><td>'+res.data.ticket[0].priority+'</td></tr>';
                str += '<tr><th>Status</th><td>'+statusbox+'</td></tr>';
                str += '<tr><th>Created Time</th><td>'+res.data.ticket[0].created_at+'</td></tr>';
                str += '<tr><th>Ticket Category</th><td>'+res.data.ticket[0].ticket_category_name+'</td></tr>';
                str += '<tr><th>Site Name</th><td>'+res.data.ticket[0].site_name+'</td></tr>';
                str += '<tr><th>Created By</th><td>'+res.data.ticket[0].username+'</td></tr>';
                str += '<tr><th>Assign To</th><td>'+selectbox+'</td></tr>';



              str += '</table>';
            //   console.log(res.data.ticket[0].t_image);
              if(res.data.ticket[0].t_image != null)
              {
              str1 = '<div style="margin-bottom: 4%;"><center><img src="'+res.data.ticket[0].t_image+'" style="width:22%"></center></div>';

              }
              else{
                str1 = "";
              }
              if((res.data.ticket[0].reason != null))
              {
                if(res.data.ticket[0].reason == 'Client Closure')
                {
                $('#reason_close').html('<option selected>Client Closure</option><option>Internal Resolution</option>');

                }
                else{
                $('#reason_close').html('<option>Client Closure</option><option selected>Internal Resolution</option>');

                }
                $('#reason_div').show();

              }
              else
              {
                $('#reason_div').hide();
                $('#reason_close').html('');
              }

              $('#ticket_comment_div').html(str+str1+html_closuer+html);

            //   if(res.data.ticket[0].status != 'close')
            //   {
                var hms = hmsToSecondsOnly(res.data.ticket[0].remaining_hours+":00");
              seconds = hms;
              var countdownTimer = setInterval('timer('+hms+')', 1000);
            //   }


				$('#equipmentModal').modal('show');
            }else{
              $('#ticket_comment_div').html('');
				$.alert({
					title: 'Alert!',
					content: 'Ticket not found',
				});
            }

            

        },
        error:function(request, status, error) {
            console.log("ajax call went wrong:" + request.responseText);
        }
    });
}

function reson_for()
{
    var status = document.getElementById('ticketstatus').value;
    if(status == 'close')
    {

        $('#reason_div').show();

        $('#reason_close').html('<option>Client Closure</option><option>Internal Resolution</option>');

    }
    else
    {
        $(';#reason_div').hide();
    }
}

function deleteData(id){
  $.confirm({
      title: 'Confirm!',
      content: 'Are you sure want to delete?',
      buttons: {
          confirm: function () {
            $('#deleteRow').attr('action', url+"/tickets/").submit();
          },
          cancel: function () {
              return true;
          }
      }
  });
}



function timer() {
  var days        = Math.floor(seconds/24/60/60);
  var hoursLeft   = Math.floor((seconds) - (days*86400));
  var hours       = Math.floor(hoursLeft/3600);
  var minutesLeft = Math.floor((hoursLeft) - (hours*3600));
  var minutes     = Math.floor(minutesLeft/60);
  var remainingSeconds = seconds % 60;
  function pad(n) {
    return (n < 10 ? "0" + n : n);
  }
  $('.ticket_timer').html(pad(days) + ":" + pad(hours) + ":" + pad(minutes) + ":" + pad(remainingSeconds));
  if (seconds == 0) {
    clearInterval(countdownTimer);
    $('.ticket_timer').html("Completed");
  } else {
    seconds--;
  }
}

</script>


@endsection
