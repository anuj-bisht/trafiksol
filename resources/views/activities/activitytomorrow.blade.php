@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">


      <div class="row">
				<div class="col-lg-12">
					<h1 class="page-header">{{__('Tomorrow Activity DPR List')}}</h1>
				</div>
				<!-- /.col-lg-12 -->
			</div>  

      
      @include('layouts.flash')
          
      
      <br/>     
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
      <table id="tableData" class="table-responsive table table-striped table-bordered" style="font-size:12px;width:100% !important">
          
          <thead>
              <tr>                                                                                                                 
                  <th>Username</th>                      
                  <th>Activity</th>                      
                  <th>Site</th>                      
                  <th>UOM</th>    
                  <th>Created</th>                                                                       
                  <th>Action</th> 
              </tr>
          </thead>
          <tbody>
                        
          </tbody>
          <tfoot>
              <tr>                                                                                                                                                                                             
                  <th>Username</th>                      
                  <th>Activity</th>                      
                  <th>Site</th>                      
                  <th>UOM</th>                                            
                  <th>Created</th>     
                  <th>Action</th> 
              </tr>
          </tfoot>
      </table>  


		</div>		
	</div>
	<!-- /#page-wrapper -->
  {!! Form::open(['method' => 'DELETE','route' => ['activities.destroy', 1],'id'=>'deleteRow','style'=>'display:inline']) !!}
      
  {!! Form::close() !!}

  <script>
        
    var url = "{{url('/')}}";
    var table = '';

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
                columns: [0, 1,2,3,4]//"thead th:not(.noExport)"
              },
              className: 'btn btn-default',
                init: function(api, node, config) {
                  $(node).removeClass('dt-button')
                },
            },
            {extend: 'pdfHtml5',
              exportOptions: {
                columns: [0, 1,2,3,4] //"thead th:not(.noExport)"
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
						"initComplete": function(settings, json) {						
              //jQuery('.popoverData').popover();
					  },
            'ajax': {
              'url': '{{ url("/") }}/activities/ajaxDprTomorrowData',
              'headers': {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
              },
              'type': 'post',
              'data': function(d) {
                d.date_from = jQuery('#textRangeFrom').val();
                d.date_to = jQuery('#textRangeTo').val();  
              },
            },          

            'columns': [                         
                             
              {
                  'data': 'username',
                  'className': 'col-md-2',
                  'render': function(data,type,row){
                    
                    return row.username;
                  }
              },               
              {
                  'data': 'activity_name',
                  'className': 'col-md-2',
                  'render': function(data,type,row){
                    
                    return row.activity_name;
                  }
              },
              {
                  'data': 'site_name',
                  'className': 'col-md-2',
                  'render': function(data,type,row){
                    
                    return row.site_name;
                  }
              },
              {
                  'data': 'uom_name',
                  'className': 'col-md-1',
                  'render': function(data,type,row){
                    
                    return row.uom_name;
                  }
              },
              {
                  'data': 'created_at',
                  'className': 'col-md-2',
                  'render': function(data,type,row){
                    
                    return row.created_at;
                  }
              },            
              {
                'data': 'Action',
                'orderable': false,
                'className': 'col-md-3',
                'render': function(data, type, row) {
                  var buttonHtml = '<a class="btn btn-primary" href="'+url+'/activities/tdprs/'+row.id+'">show</a>';
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

  });



function deleteData(id){

  $.confirm({
      title: 'Confirm!',
      content: 'Are you sure want to delete?',
      buttons: {
          confirm: function () {
            $('#deleteRow').attr('action', url+"/activities/").submit();  
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
          content: 'Please select at least one Activity to approve/reject!',
      });
      return false;
    }

    $.confirm({
        title: 'Confirm!',
        content: 'Are you sure want to Approve/Reject?',
        buttons: {
            confirm: function () {
              var url = "{{url('/')}}/activities/approveRejectActivity"; 
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
