@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">


      <div class="row">
				<div class="col-lg-12">
					<h1 class="page-header">{{__('Manpower Attendance List')}}</h1>
				</div>
				<!-- /.col-lg-12 -->
			</div>  

      
      @include('layouts.flash')

      <div class="row">        
        <div class="col-lg-4">
          <select id="typeFilter" class="form-control">
              <option value="0">Select User Type</option>
              @if(count($usertypes)>0)
                @foreach($usertypes as $v)
                  <option value="{{$v->id}}">{{$v->name}}</option>
                @endforeach
              @endif          
          </select>
        </div>    
        <div class="col-lg-8">
          <div class="row" >
            <div class="col-md-5">
              <input id="textRangeFrom" placeholder="Date From" type="text" class="form-control" value=""/><span class="calendarIcon">          
            </div>
            <div class="col-md-5">                    
              <input id="textRangeTo" placeholder="Date To" type="text"  class="form-control" value=""/><span class="calendarIcon">
            </div>
            <div class="col-md-2">             
              <button id="dateRangeBtn" class="btn btn-primary">Go</button>
            </div>
          </div>
        </div>      

      </div>
      

      <br/>
            
      <table id="tableData" class="table-responsive table table-striped table-bordered" style="font-size:12px;width:100% !important">
          
          <thead>
              <tr>
                  <th>User Type</th>                                                                                                  
                  <th>Name</th>                      
                  <th>Attendance</th> 
                  <th>Created</th>                   
              </tr>
          </thead>
          <tbody>
                        
          </tbody>
          <tfoot>
              <tr>                  
                  <th>User Type</th>                                                          
                  <th>Name</th>                      
                  <th>Attendance</th> 
                  <th>Created</th>                   
              </tr>
          </tfoot>
      </table>  


		</div>		
	</div>
	<!-- /#page-wrapper -->
 

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
                columns: [0, 1,2]//"thead th:not(.noExport)"
              },
              className: 'btn btn-default',
                init: function(api, node, config) {
                  $(node).removeClass('dt-button')
                },
            },
            {extend: 'pdfHtml5',
              exportOptions: {
                columns: [0, 1,2] //"thead th:not(.noExport)"
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
            "order": [[ 1, "desc" ]],
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
              'url': '{{ url("/") }}/attendances/ajaxManpowerData',
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
                  'data': 'type_name',
                  'className': 'col-md-2',
                  'render': function(data,type,row){
                    
                    return row.type_name;
                  }
              },                
              {
                  'data': 'name',
                  'className': 'col-md-2',
                  'render': function(data,type,row){
                    
                    return row.name;
                  }
              },
              {
                  'data': 'attendance',
                  'className': 'col-md-2',
                  'render': function(data,type,row){
                    
                    return row.attendance;
                  }
              },
              {
                  'data': 'created_at',
                  'className': 'col-md-2',
                  'orderable': false,
                  'render': function(data,type,row){
                    
                    return row.created;
                  }
              }
            ]
          });   


          $('#typeFilter').change(function () {	    
            table.draw();
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




</script>


@endsection
