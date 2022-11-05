@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">


      <div class="row">
				<div class="col-lg-12">
					<h1 class="page-header">{{__('Hardware Request List')}}</h1>
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
      <table id="tableData" class="table-responsive table table-striped table-bordered" style="font-size:12px;width:100% !important">
          
          <thead>
              <tr>                                                                            
				  <th>&nbsp;</th>
                  <th>Subject</th>                                        
                  <th>Username</th>                                        
                  <th>Equipment Required</th>                                        
                  <th>Quantity</th>                                        
                  <th>Reason</th>                                        
                  <th>Ref No</th>                                                          
                  <th>Images</th>      
                  <th>Status</th>
                  <th>Created</th>                                                          
              </tr>
          </thead>
          <tbody>
                        
          </tbody>
          <tfoot>
              <tr>      
				  <th>&nbsp;</th>                                                                      
                  <th>Subject</th>                                        
                  <th>Username</th>                                        
                  <th>Equipment Required</th>                                        
                  <th>Quantity</th>                                        
                  <th>Reason</th>                                        
                  <th>Ref No</th>      
                  <th>Images</th>      
                  <th>Status</th>                                  
                  <th>Created</th>                                                          
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
                columns: [0, 1,3,4,5,7]//"thead th:not(.noExport)"
              },
              className: 'btn btn-default',
                init: function(api, node, config) {
                  $(node).removeClass('dt-button')
                },
            },
            {extend: 'pdfHtml5',
              exportOptions: {
                columns: [0, 1,3,4,5,7] //"thead th:not(.noExport)"
              },     
              filename: function() {
                return 'hardware-request'      
              },
              title: function() {
                return 'Hardware Request list'
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
              if (aData["status"] == "approved") {
                jQuery('td', nRow).css('background-color', '#6fdc6f');
              } else if (aData["status"] == "rejected") {
                jQuery('td', nRow).css('background-color', '#ff7f7f');
              }
              
            },
						"initComplete": function(settings, json) {						
              //jQuery('.popoverData').popover();
					  },
            'ajax': {
              'url': '{{ url("/") }}/tickets/hwrequestAjax',
              'headers': {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
              },
              'type': 'post',
              'data': function(d) {
                
                //d.hourFilter = jQuery("#hourFilter option:selected").val();   
              },
            },          

            'columns': [
                              
              {
                'data': 'hwids_ids',
                'orderable': false,
                'className': 'col-md-1',                
                'render': function(data, type, row) {
                  if(row.status != 'open'){
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
                  'data': 'equipment_required',
                  'className': 'col-md-1',
                  'render': function(data,type,row){
                    
                    return row.equipment_required;
                  }
              },
              {
                  'data': 'quantity',
                  'className': 'col-md-1',
                  'render': function(data,type,row){
                    
                    return row.quantity;
                  }
              },
              {
                  'data': 'reason',
                  'className': 'col-md-1',
                  'render': function(data,type,row){
                    
                    return row.reason;
                  }
              },
              {
                  'data': 'ref_no',
                  'className': 'col-md-1',
                  'render': function(data,type,row){
                    
                    return row.ref_no;
                  }
              },
              {
                  'data': 'path',
                  'className': 'col-md-1',
                  'render': function(data,type,row){
                    
                        return "<img src=\"{{ URL::asset('/') }}"+ row.path +"\" height=\"50\"/>";
                                      
                  }
              },
              {
                  'data': 'status',
                  'className': 'col-md-1',
                  'render': function(data,type,row){
                                        
                    return row.status;
                  }
              },
              {
                  'data': 'created_at',
                  'className': 'col-md-1',
                  'render': function(data,type,row){
                    
                    return row.created_at;
                  }
              }
            ]
          });   

                
});



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
          content: 'Please select at least one hardware Request to approve/reject!',
      });
      return false;
    }

    $.confirm({
        title: 'Confirm!',
        content: 'Are you sure want to Approve/Reject?',
        buttons: {
            confirm: function () {
              var url = "{{url('/')}}/tickets/approveRejectHWRequest"; 
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
