@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">


      <div class="row">
				<div class="col-lg-12">
					<h1 class="page-header">{{__('Ticket Pause Request List')}}</h1>
				</div>
				<!-- /.col-lg-12 -->
			</div>  
      

      @include('layouts.flash')
				
      <br/> 
      <div class="row">
        <div class="col-lg-12">
            <div class="pull-left">
                <button class="btn btn-success" onclick="isCheckboxChecked('1')">Approve</button>
                <button class="btn btn-danger" onclick="isCheckboxChecked('0')">Reject</button>
            </div>      
                  
        </div>
        
      </div>
      <br/> 
      <div class="row">
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
      </div>
      <br/>            
      <table id="tableData" class="table-responsive table table-striped table-bordered" style="font-size:12px;width:100% !important">
          
          <thead>
              <tr>           
				  <th>&nbsp;</th>                                                                     
                  <th>Subject</th>                                        
                  <th>Username</th>   
                  <th>Pause From</th>                                                                            
                  <th>Pause To</th>                                                                           
                  <th>Reason</th>  
                  <th>Status</th>  
                  <th>Created</th>                                                          
                  <th>Action</th>                                                          
              </tr>
          </thead>
          <tbody>
                        
          </tbody>
          <tfoot>
              <tr>       
				  <th>&nbsp;</th>                                                                         
                  <th>Subject</th>                                        
                  <th>Username</th>     
                  <th>Pause From</th>                                                                            
                  <th>Pause To</th>                                                                            
                  <th>Reason</th>  
                  <th>Status</th>  
                  <th>Created</th>        
                  <th>Action</th>                                                              
              </tr>
          </tfoot>
      </table>  


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
                columns: [0, 1,2,3,4,5,6]//"thead th:not(.noExport)"
              },
              className: 'btn btn-default',
                init: function(api, node, config) {
                  $(node).removeClass('dt-button')
                },
            },
            {extend: 'pdfHtml5',
              exportOptions: {
                columns: [0, 1,2,3,4,5,6] //"thead th:not(.noExport)"
              },     
              filename: function() {
                return 'ticket-pause-request'      
              },
              title: function() {
                return 'Ticket Pause Request list'
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
              //console.log(aData.status)
              
            },
						"initComplete": function(settings, json) {						
              //jQuery('.popoverData').popover();
					  },
            'ajax': {
              'url': '{{ url("/") }}/tickets/pauseAjax',
              'headers': {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
              },
              'type': 'post',
              'data': function(d) {
                
                d.siteFilter = jQuery("#siteFilter option:selected").val();     
              },
            },          

            'columns': [
               {
                'data': 'pause_ids',
                'orderable': false,
                'className': 'col-md-1',                
                'render': function(data, type, row) {
                  if(row.is_approved != 'N'){
                    var buttonHtml = '<input type="checkbox" disabled="disabled" class="form-control" name="ids[]" value="'+row.id+'">';
                  }else{
                    var buttonHtml = '<input type="checkbox" class="form-control" name="ids[]" value="'+row.id+'">';
                  }
                    
                  return buttonHtml;
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
                  'data': 'username',
                  'className': 'col-md-1',
                  'render': function(data,type,row){
                    
                    return row.username;
                  }
              },
              {
                  'data': 'pause_from',
                  'className': 'col-md-2',
                  'render': function(data,type,row){
                    
                    if(row.start=='N'){
                      return 'N/A';
                    }else{
                      return row.pause_from;
                    }
                    
                  }
              },
              {
                  'data': 'pause_to',
                  'className': 'col-md-2',
                  'render': function(data,type,row){
                    if(row.end=='Y'){
                      return row.pause_to;
                    }else{
                      return 'N/A';
                    }
                    
                  }
              },
              {
                  'data': 'reason',
                  'className': 'col-md-2',
                  'render': function(data,type,row){
                    
                    return row.reason;
                  }
              },
              {
                  'data': 'status',
                  'className': 'col-md-1',
                  'render': function(data,type,row){
                    var status = 'New';
                    if(row.is_approved=='Y'){
                      status = 'Approved'
                    }else if(row.is_approved=='R'){
                      status = 'Rejected'
                    }
                    return status;
                  }
              },
              {
                  'data': 'created_at',
                  'className': 'col-md-1',
                  'render': function(data,type,row){
                    
                    return row.created_at;
                  }
              },
              {
                  'data': 'Action',
                  'className': 'col-md-1',
                  'render': function(data,type,row){                    
                    if(row.is_approved=='Y' && row.start=='N'){
                      var buttonHtml = '<button class="btn btn-success" onclick="ticketPauseStartStop('+row.id+',\'start\')">Start Pause</button>';
                      return buttonHtml;
                    }
                    if(row.is_approved=='Y' && row.start=='Y' && row.end=='N'){
                      var buttonHtml = '<button class="btn btn-danger" onclick="ticketPauseStartStop('+row.id+',\'stop\')">Stop Pause</button>';
                      return buttonHtml;
                    }
                    if(row.is_approved=='Y' && row.start=='Y' && row.end=='Y'){
                      var buttonHtml = 'Completed';
                      return buttonHtml;
                    }
                    return '';
                  }
              }
            ]
          });   


           $('#siteFilter').change(function () {	    
            table.draw();
          });       
});

function ticketPauseStartStop(id,type){
  $.confirm({
      title: 'Confirm!',
      content: 'Are you sure want to '+type+' pause request timer?',
      buttons: {
          confirm: function () {
            var url = "{{url('/')}}/tickets/ticketPauseStartStop"; 
            $.ajax({
                  type: "POST",
                  url: url,      
                  data: {
                    "_token": $('meta[name="csrf-token"]').attr('content'),
                      "type": type,"id":id
                  },
                  success: function(res) {
                      if(res.status){
                        table.ajax.reload();  
                      }else{
                        jQuery.alert({
                            title: 'Alert!',
                            content: 'Start/Stop Error!!',
                        });
                        return false;
                      } 
                      
                  },
                  error:function(request, status, error) {
                      console.log("ajax call went wrong:" + request.responseText);
                  }
            });
          },
          cancel: function () {
              return true;
          }
      }
  });
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

function isCheckboxChecked(val){
    var searchIDs = $("#tableData input:checkbox:checked").map(function(){
      return $(this).val();
    }).get(); // <----

    if(!searchIDs.length){
      jQuery.alert({
          title: 'Alert!',
          content: 'Please select at least one Pause Request to approve/reject!',
      });
      return false;
    }

    $.confirm({
        title: 'Confirm!',
        content: 'Are you sure want to Approve/Reject?',
        buttons: {
            confirm: function () {
              var url = "{{url('/')}}/tickets/approveRejectPause"; 
              $.ajax({
                  type: "POST",
                  url: url,      
                  data: {
                    "_token": $('meta[name="csrf-token"]').attr('content'),
                      "type": val,"ids":searchIDs            
                  },
                  success: function(res) {
                      if((res.status) && (res.data.length > 0)){
                        
                      }else{
                        
                      } 

                      table.ajax.reload();

                  },
                  error:function(request, status, error) {
                      console.log("ajax call went wrong:" + request.responseText);
                  }
              });
            },
            cancel: function () {
                return true;
            }
        }
    });
            
}  

 

</script>


@endsection
