@extends('layouts.app')
<script src="{{ asset('js/jquery-3.5.1.js') }}"></script>
@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">


      <div class="row">
				<div class="col-lg-12">
					<h1 class="page-header">{{__('Site List')}}</h1>
				</div>
				<!-- /.col-lg-12 -->
			</div>  

      <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>&nbsp;</h2>
            </div>   
            <div class="pull-right">
                <a class="btn btn-default" href="{{ route('sites.create') }}"> Create New Site</a>
                <a class="btn btn-default report-btn-action" href="#">DPR Report</a>
                <a  href="#" class="btn btn-default site-advance-btn-action">Add Advance</a>
                <a  href="#" class="btn btn-default site-sla-btn-action">Site SLA</a>
                <a  href="#" class="btn btn-default equipment-btn-action"> Add Equipment</a>
                <a class="btn btn-default activity-btn-action"  href="#"> Add Activity</a>
                <a class="btn btn-default vehicle-btn-action" href="#"> Add Vehicle</a>
            </div>      
        </div>
      </div>

      @include('layouts.flash')
            
      <table id="tableData" class="table-responsive table table-striped table-bordered" style="font-size:12px;width:100% !important">
          
          <thead>
            <tr>               
              <th>&nbsp;</th>
              <th>Name</th>
              <th>Alias</th>
              <th>Project</th>
              <th>Client</th>
              <th>Advance</th>
              <th>Location</th>              
              <th>City</th>    
              <th>Stretch</th>      
              <th width="280px">Action</th>
            </tr>
          </thead>
          <tbody>
                        
          </tbody>
          <tfoot>
            <tr>               
              <th>&nbsp;</th>
              <th>Name</th>
              <th>Alias</th>
              <th>Project</th>
              <th>Client</th>
              <th>Advance</th>
              <th>Location</th>              
              <th>City</th>     
              <th>Stretch</th>     
              <th width="280px">Action</th>
            </tr>
          </tfoot>
      </table>  


		</div>		
	</div>

  {!! Form::open(['method' => 'DELETE','route' => ['sites.destroy', 1],'id'=>'deleteRow','style'=>'display:inline']) !!}
      
  {!! Form::close() !!}

	<!-- /#page-wrapper -->
 

  <script>
        
    var url = "{{url('/')}}";
    var table = '';

    jQuery(document).ready(function() {
          
          //jQuery('#tableData').append('<caption style="caption-side: bottom">A fictional company\'s staff table.</caption>');
 
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
                columns: [1,2,3,4,5,6,7,8]//"thead th:not(.noExport)"
              },
              className: 'btn btn-default',
                init: function(api, node, config) {
                  $(node).removeClass('dt-button')
                },
            },
            {extend: 'pdfHtml5',
              exportOptions: {
                columns: [1,2,3,4,5,6,7,8] //"thead th:not(.noExport)"
              },
              title: 'Site Details',              
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
                    doc.styles.title = {
                      color: 'black',
                      fontSize: '20',                      
                      alignment: 'left'
                    }  
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
              'url': '{{ url("/") }}/sites/ajaxData',
              'headers': {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
              },
              'type': 'post',
              'data': function(d) {
                //d.statusFilter = jQuery('#statusFilter').val();
                d.parent = jQuery('#parentFilter option:selected').val();
                //d.search = jQuery("#msds-select option:selected").val();
              },
            },          

            'columns': [
                              
              {
                  'data': 'checkboxis',        
                  'orderable': false,          
                  'className': 'col-md-1',
                  'render': function(data,type,row){                    
                    return '<input type="radio" name="site_chkbox" class="site_chkbox" value="'+row.id+'">';
                  }
              },
              {
                  'data': 'name',
                  'className': 'col-md-1',                  
                  'render': function(data,type,row){                    
                    return row.name;
                  }
              },
              {
                  'data': 'alias_name',
                  'className': 'col-md-1',                  
                  'render': function(data,type,row){
                    
                    return row.alias_name;
                  }
              },
              {
                  'data': 'project',
                  'className': 'col-md-1',                  
                  'render': function(data,type,row){
                    
                    return row.project_name;
                  }
              },
              {
                  'data': 'client',
                  'className': 'col-md-1',                  
                  'render': function(data,type,row){
                    
                    return row.client_name;
                  }
              },
              {
                  'data': 'advance',
                  'className': 'col-md-1',                  
                  'render': function(data,type,row){
                    
                    return row.advance_sum;
                  }
              },
              {
                  'data': 'location',
                  'className': 'col-md-1',                  
                  'render': function(data,type,row){
                    
                    return row.location;
                  }
              },
              {
                  'data': 'city',
                  'className': 'col-md-1',                  
                  'render': function(data,type,row){
                    
                    return row.city;
                  }
              },
              {
                  'data': 'stretch',
                  'className': 'col-md-1',                  
                  'render': function(data,type,row){
                    
                    return row.stretch_from+' - '+row.stretch_to;
                  }
              },
              {
                'data': 'Action',
                'orderable': false,
                'className': 'col-md-3',
                'render': function(data, type, row) {
                  var buttonHtml = '<a class="btn btn-info" href="'+url+'/sites/'+row.id+'">Show</a>&nbsp;&nbsp;<a class="btn btn-primary" href="'+url+'/sites/'+row.id+'/edit">Edit</a>&nbsp;&nbsp;<a class="btn btn-danger" href="javascript:void(0);" onclick="deleteData('+row.id+')">Delete</a>';
                  return buttonHtml;
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
              $('#deleteRow').attr('action', url+"/sites/"+id).submit();  
            },
            cancel: function () {
                return true;
            }
        }
    });
  }    

</script>

@endsection


<div class="modal fade" id="siteSlaModal" tabindex="-1" role="dialog" aria-labelledby="siteSlaModal" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="siteSlaModalLabel">Site SLA Assignment</h4>
      </div>
        {!! Form::open(array('route' => 'sites.store','method'=>'POST','id'=>'site_sla_model_form')) !!}
          <input type="hidden" name="hidden_site_sla_id" id="hidden_site_sla_id" value="">
          <div class="modal-body">
            <table class="table table-bordered" id="equipment_model_table">
              <thead>
              <tr>               
                <th>&nbsp;</th>                     
                <th>SLA Name</th>                
              </tr>
              </thead>
              <tbody id="site_sla_tr">
                
              <tbody>
            </table>                              
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Assign</button>
          </div>
        {!! Form::close() !!}
    </div>
  </div>
</div>  


<div class="modal fade" id="advanceModal" tabindex="-1" role="dialog" aria-labelledby="advanceModal" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="equipmentModalLabel">Advance Assignment</h4>
      </div>
      {!! Form::open(array('route' => 'sites.store','method'=>'POST','id'=>'advance_model_form')) !!}
        <input type="hidden" name="advance_hidden_site_id" id="advance_hidden_site_id" value="">
        <div class="modal-body">
        <table class="table table-bordered" id="equipment_model_table">
          <thead>
          <tr>                      
            <th>Site Name</th>            
            <th>Amount</th>
            <th>Reason</th>
            <th>Advance Paid To</th>                        
            <th>&nbsp;</th>     
          </tr>
          </thead>
          <tbody id="advance_modal_tr">
            
          <tbody>
         </table>        
          
         <div class="col-xs-4 col-sm-4 col-md-4">
              <div class="form-group">
                  <strong>Amount:</strong>
                  {!! Form::text('amount', null, array('placeholder' => 'Amount','required'=>'true','class' => 'form-control')) !!}
              </div>
          </div> 
          <div class="col-xs-4 col-sm-4 col-md-4">
              <div class="form-group">
                  <strong>Reason:</strong>
                  {!! Form::textarea('reason', null, array('required'=>'true','rows'=>'3','class' => 'form-control','placeholder'=>'Enter Reason')) !!}
              </div>
          </div>

          <div class="col-xs-4 col-sm-4 col-md-4">
              <div class="form-group">
                  <strong>Paid To:</strong>
                  {!! Form::select('paid_to', $users, null, array('required'=>'true','class' => 'form-control','placeholder'=>'Advance given to user')) !!}
              </div>
          </div>
                           
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Assign</button>
        </div>
        {!! Form::close() !!}
    </div>
  </div>
</div>  



<div class="modal fade" id="equipmentModal" tabindex="-1" role="dialog" aria-labelledby="equipmentModal" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="equipmentModalLabel">Equipment Assignment</h4>
      </div>
      {!! Form::open(array('route' => 'sites.store','method'=>'POST','id'=>'equipment_model_form')) !!}
        <input type="hidden" name="equipment_hidden_site_id" id="equipment_hidden_site_id" value="">
        <div class="modal-body">
        <table class="table table-bordered" id="equipment_model_table">
          <thead>
          <tr>                                  
            <th>Site Name</th>
            <th>Brand Name</th>
            <th>Model</th>
            <th>Equipment</th>            
            <th>Chainage</th>      
            <th>Sla name</th>                    
            <th>&nbsp;</th>     
          </tr>
          </thead>
          <tbody id="equipment_modal_tr">
            
          <tbody>
         </table>        
          
          <div class="col-xs-2 col-sm-2 col-md-2">
              <div class="form-group">
                  <strong>Vendor:</strong>
                  {!! Form::select('vendor', $vendors,null, array('required'=>'true','id'=>'data_vendor_id','class' => 'form-control','placeholder'=>'Select Vendor')) !!}
              </div>
          </div>
          <div class="col-xs-2 col-sm-2 col-md-2">
              <div class="form-group">
                  <strong>Brand:</strong>
                  {!! Form::select('brand', [],null, array('required'=>'true','id'=>'main_brand_id','class' => 'form-control','placeholder'=>'Select Brand')) !!}
              </div>
          </div>
          <div class="col-xs-3 col-sm-3 col-md-2">
              <div class="form-group">
                  <strong>Model:</strong>
                  {!! Form::select('model_id', [],null, array('required'=>'true','id'=>'model_id','class' => 'form-control','placeholder'=>'Select Model')) !!}
              </div>
          </div>
          <div class="col-xs-3 col-sm-3 col-md-2">
              <div class="form-group">
                  <strong>Equipment:</strong>
                  {!! Form::select('equipment_id', [],null, array('required'=>'true','id'=>'equipment_id_DD','class' => 'form-control','placeholder'=>'Select Equipment')) !!}
              </div>
          </div>
          <div class="col-xs-2 col-sm-2 col-md-2">
              <div class="form-group">
                  <strong>Chainage:</strong>
                  {!! Form::text('equipment_chainage', null, array('placeholder' => 'Chainage','required'=>'true','class' => 'form-control')) !!}
              </div>
          </div>                   
          <div class="col-xs-2 col-sm-2 col-md-2">
              <div class="form-group">
                  <strong>Equipment Sla:</strong>
                  {!! Form::select('equipment_sla', $sla_list,null, array('placeholder' => 'SLA','required'=>'true','class' => 'form-control')) !!}
              </div>
          </div>                   
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Assign</button>
        </div>
        {!! Form::close() !!}
    </div>
  </div>
</div>  



<div class="modal fade" id="activityModal" tabindex="-1" role="dialog" aria-labelledby="activityModal" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="activityModalLabel">Activity Assignment</h4>
      </div>
      {!! Form::open(array('route' => 'sites.store','method'=>'POST','id'=>'activity_model_form')) !!}
        <input type="hidden" name="activity_hidden_site_id" id="activity_hidden_site_id" value="">
        <div class="modal-body">
        <table class="table table-bordered" id="activity_model_table">
          <thead>
          <tr>                      
            <th>Project Name</th>
            <th>Site Name</th>
            <th>Activity Category</th>
            <th>Activity name</th>            
            <th>Quantity</th>      
            <th>&nbsp;</th>                  
          </tr>
          </thead>
          <tbody id="activity_modal_tr">
            
          <tbody>
         </table>        
          
          <div class="col-xs-3 col-sm-3 col-md-3">
              <div class="form-group">
                  <strong>activity Category:</strong>
                  {!! Form::select('main_activity_category', $activityCategoryList,null, array('required'=>'true','id'=>'main_activity_category','class' => 'form-control','placeholder'=>'Select Category')) !!}
              </div>
          </div>
          <div class="col-xs-3 col-sm-3 col-md-3">
              <div class="form-group">
                  <strong>Activity Sub Category:</strong>
                  <select name="activity_category_id" id="activitySubCategoryeDD" class="form-control">
                      <option value="0">Activity Sub Category</option>                            
                  <select>                        
              </div>
          </div>
          <div class="col-xs-3 col-sm-3 col-md-3">
              <div class="form-group">
                  <strong>Activity:</strong>
                  {!! Form::select('activity_id', [],null, array('required'=>'true','id'=>'activity_DD','class' => 'form-control','placeholder'=>'Select Activity')) !!}
              </div>
          </div>
          <div class="col-xs-3 col-sm-3 col-md-3">
              <div class="form-group">
                  <strong>Quantity:</strong>
                  {!! Form::text('activity_quantity', null, array('placeholder' => 'Quantity','required'=>'true','class' => 'form-control')) !!}
              </div>
          </div>                   
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Assign</button>
        </div>
        {!! Form::close() !!}
    </div>
  </div>
</div>  




<div class="modal fade" id="vehicleModal" tabindex="-1" role="dialog" aria-labelledby="vehicleModal" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="vehicleModalLabel">Vehicle Assignment</h4>
      </div>
      {!! Form::open(array('route' => 'sites.store','method'=>'POST','id'=>'vehicle_model_form')) !!}
        <input type="hidden" name="hidden_site_id" id="hidden_site_id" value="">
        <div class="modal-body">
        <table class="table table-bordered" id="vehicle_model_table">
          <thead>
          <tr>                      
            <th>Project Name</th>
            <th>Site Name</th>
            <th>Vehicle Type</th>
            <th>Vehicle name</th>
            <th>Vehicle Number</th>
            <th>Quantity</th>    
            <th>&nbsp;</th>                    
          </tr>
          </thead>
          <tbody id="vehicle_modal_tr">
            
          <tbody>
         </table>        
          
          <div class="col-xs-4 col-sm-4 col-md-4">
              <div class="form-group">
                  <strong>Vehicle Type:</strong>
                  {!! Form::select('type_vehicle_id', $typeVehicleList,null, array('required'=>'true','id'=>'type_vechicle_DD','class' => 'form-control','placeholder'=>'Select vehicle type')) !!}
              </div>
          </div>
          <div class="col-xs-4 col-sm-4 col-md-4">
              <div class="form-group">
                  <strong>Vehicle:</strong>
                  {!! Form::select('vehicle_id', [],null, array('required'=>'true','id'=>'vehicle_DD','class' => 'form-control','placeholder'=>'Select vehicle')) !!}
              </div>
          </div>
          <div class="col-xs-4 col-sm-4 col-md-4">
              <div class="form-group">
                  <strong>Quantity:</strong>
                  {!! Form::text('vehicle_quantity', null, array('placeholder' => 'Quantity','required'=>'true','class' => 'form-control')) !!}
              </div>
          </div>                   
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Assign</button>
        </div>
        {!! Form::close() !!}
    </div>
  </div>
</div>  
<script>

function isRadioChecked(){
    if($("input[type='radio'].site_chkbox").is(':checked')) {
        var chkedval = $("input[type='radio'].site_chkbox:checked").val();        
        return chkedval;
    }else{
      return 0;
    }
}  



$(document).ready(function(){

  $('#vehicle_model_form').submit(function(e){
    e.preventDefault();

    $.ajax({
        type: "POST",
        url: "{{url('/')}}/sites/assignVehicleToSite",    
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: $('#vehicle_model_form').serialize(),           
        success: function(res) {
            if(res.status){
              $('.vehicle-btn-action').trigger('click');
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

  $('#activity_model_form').submit(function(e){
    e.preventDefault();

    $.ajax({
        type: "POST",
        url: "{{url('/')}}/sites/assignActivityToSite",    
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: $('#activity_model_form').serialize(),           
        success: function(res) {
            if(res.status){
              $('.activity-btn-action').trigger('click');
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

  $('#advance_model_form').submit(function(e){
    e.preventDefault();

    $.ajax({
        type: "POST",
        url: "{{url('/')}}/sites/assignAdvanceToSite",    
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: $('#advance_model_form').serialize(),           
        success: function(res) {
            if(res.status){
              $('.site-advance-btn-action').trigger('click');
            }else{
              jQuery.alert({
                  title: 'Alert!',
                  content: res.message,
              });
            }                      
                      
        },
        error:function(request, status, error) {
            console.log("ajax call went wrong:" + request.responseText);
        }
    });
    
  });
  

  $('#equipment_model_form').submit(function(e){
    e.preventDefault();

    $.ajax({
        type: "POST",
        url: "{{url('/')}}/sites/assignEquipmentToSite",    
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: $('#equipment_model_form').serialize(),           
        success: function(res) {
            if(res.status){
              $('.equipment-btn-action').trigger('click');
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



  $('#site_sla_model_form').submit(function(e){
    e.preventDefault();
    $.ajax({
        type: "POST",
        url: "{{url('/')}}/sites/assignSlaToSite",    
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: $('#site_sla_model_form').serialize(),           
        success: function(res) {
            if(res.status){
              $('#siteSlaModal').modal('hide'); 
            }else{
              jQuery.alert({
                  title: 'Alert!',
                  content: res.message,
              });
            }                              
        },
        error:function(request, status, error) {
            console.log("ajax call went wrong:" + request.responseText);
        }
    });
    
  });

  


$('#model_id').on('change',function(){
  var model_id = $(this).val();
  var url = "{{url('/')}}/equipments/getEquipmentByModelId"; 
  if(model_id=="" || model_id==0){
    return false;
  }
  $.ajax({
      type: "POST",
      url: url,      
      data: {
        "_token": $('meta[name="csrf-token"]').attr('content'),
        "model_id": model_id            
      },
      success: function(res) {
          if((res.status) && (res.data.length > 0)){
            var html = '';
            jQuery.each(res.data, function(index, item) {
                html += '<option value="'+item.id+'">'+item.title+'</option>';                                                
            });
            $('#equipment_id_DD').empty().append(html);
          }else{
            $('#equipment_id_DD').empty().append('<option value="0">Select Equipment</option>');
          }                                                  
      },
      error:function(request, status, error) {
          console.log("ajax call went wrong:" + request.responseText);
      }
  });
});




$('.report-btn-action').on('click',function(){
  var checkedSiteVal = isRadioChecked();
  if(!checkedSiteVal){
    jQuery.alert({
        title: 'Alert!',
        content: 'Please select a site from list!',
    });
    return false;
  }
  var url = "{{url('/')}}/generateReportPDF/"+checkedSiteVal; 

  window.open(url);
  
});


$('.equipment-btn-action').on('click',function(){

  var checkedSiteVal = isRadioChecked();

  if(!checkedSiteVal){
    jQuery.alert({
        title: 'Alert!',
        content: 'Please select a site from list!',
    });
    return false;
  }
  //$('#vehicleModal').modal('show'); 
  $('#equipment_hidden_site_id').val(checkedSiteVal);
  var url = "{{url('/')}}/sites/getAssignedEquipmentAjax"; 

  $.ajax({
      type: "POST",
      url: url,      
      data: {
        "_token": $('meta[name="csrf-token"]').attr('content'),
        "site_id": checkedSiteVal            
      },
      success: function(res) {
          if((res.status) && (res.data.length > 0)){
            var html = '';
            jQuery.each(res.data, function(index, item) {
                html += '<tr>';                
                html += "<td>"+item.site_name+"</td>";                
                html += "<td>"+item.brand_name+"</td>";                
                html += "<td>"+item.model_name+"</td>";                
                html += "<td>"+item.equipment_name+"</td>";                
                html += "<td>"+item.chainage+"</td>";
                html += "<td>"+item.sla_name+"</td>";
                html += "<td><span onclick='deleteSiteAssignment("+item.site_equipment_id+",\"equipment\")' style='color:red;cursor:pointer;' class='glyphicon glyphicon-trash'></span></td>";
                html += '</tr>';
                
            });
            $('#equipment_modal_tr').html(html);
          }else{
            $('#equipment_modal_tr').html('<td colspan="7" style="text-align:center;"><i>No equipment assigned</i></td>');
          }          
          
          $('#equipmentModal').modal('show'); 
                    
      },
      error:function(request, status, error) {
          console.log("ajax call went wrong:" + request.responseText);
      }
  });
});


$('.site-advance-btn-action').on('click',function(){

    var checkedSiteVal = isRadioChecked();

    if(!checkedSiteVal){
      jQuery.alert({
          title: 'Alert!',
          content: 'Please select a site from list!',
      });
      return false;
    }
    //$('#vehicleModal').modal('show'); 
    $('#advance_hidden_site_id').val(checkedSiteVal);
    var url = "{{url('/')}}/sites/getAssignedAdvanceAjax"; 

    $.ajax({
        type: "POST",
        url: url,      
        data: {
          "_token": $('meta[name="csrf-token"]').attr('content'),
          "site_id": checkedSiteVal            
        },
        success: function(res) {
            if((res.status) && (res.data.length > 0)){
              var html = '';
              jQuery.each(res.data, function(index, item) {
                  html += '<tr>';
                  html += "<td>"+item.site_name+"</td>";
                  html += "<td>"+item.amount+"</td>";                
                  html += "<td>"+item.reason+"</td>";                
                  html += "<td>"+item.username+"</td>";  
                  html += "<td><span onclick='deleteSiteAssignment("+item.id+",\"advance\")' style='color:red;cursor:pointer;' class='glyphicon glyphicon-trash'></span></td>";                                
                  html += '</tr>';
                  
              });
              $('#advance_modal_tr').html(html);
            }else{
              $('#advance_modal_tr').html('<td colspan="5" style="text-align:center;"><i>No Advance Given</i></td>');
            }          
            
            $('#advanceModal').modal('show'); 
                      
        },
        error:function(request, status, error) {
            console.log("ajax call went wrong:" + request.responseText);
        }
    });
});

/**----------Activity code start------------ */

$('#activitySubCategoryeDD').on('change',function(){
  var activity_category_id = $(this).val();
  var url = "{{url('/')}}/activities/getActivityByCategoryId"; 
  if(activity_category_id=="" || activity_category_id==0){
    return false;
  }
  $.ajax({
      type: "POST",
      url: url,      
      data: {
        "_token": $('meta[name="csrf-token"]').attr('content'),
        "category_id": activity_category_id            
      },
      success: function(res) {
          if((res.status) && (res.data.length > 0)){
            var html = '';
            jQuery.each(res.data, function(index, item) {
                html += '<option value="'+item.id+'">'+item.name+'</option>';                                                
            });
            $('#activity_DD').empty().append(html);
          }else{
            $('#activity_DD').empty().append('<option value="0">Select Activity</option>');
          }                                                  
      },
      error:function(request, status, error) {
          console.log("ajax call went wrong:" + request.responseText);
      }
  });
});


$('.activity-btn-action').on('click',function(){

  var checkedSiteVal = isRadioChecked();

  if(!checkedSiteVal){
    jQuery.alert({
        title: 'Alert!',
        content: 'Please select a site from list!',
    });
    return false;
  }
  //$('#vehicleModal').modal('show'); 
  $('#activity_hidden_site_id').val(checkedSiteVal);
  var url = "{{url('/')}}/sites/getAssignedActivityAjax"; 

  $.ajax({
      type: "POST",
      url: url,      
      data: {
        "_token": $('meta[name="csrf-token"]').attr('content'),
        "site_id": checkedSiteVal            
      },
      success: function(res) {
          if((res.status) && (res.data.length > 0)){
            var html = '';
            jQuery.each(res.data, function(index, item) {
                html += '<tr>';
                html += "<td>"+item.project_name+"</td>";
                html += "<td>"+item.site_name+"</td>";                
                html += "<td>"+item.activity_category_name+"</td>";
                html += "<td>"+item.activity_name+"</td>";              
                html += "<td>"+item.quantity+"</td>";
                html += "<td><span onclick='deleteSiteAssignment("+item.site_activity_id+",\"activity\")' style='color:red;cursor:pointer;' class='glyphicon glyphicon-trash'></span></td>";
                html += '</tr>';
                
            });
            $('#activity_modal_tr').html(html);
          }else{
            $('#activity_modal_tr').html('<td colspan="6" style="text-align:center;"><i>No Activity assigned</i></td>');
          }          
          
          $('#activityModal').modal('show'); 
                    
      },
      error:function(request, status, error) {
          console.log("ajax call went wrong:" + request.responseText);
      }
  });
});



/*---------Activity code end------------*/

/**---------------Add vehicle code start--------------- */

$('.vehicle-btn-action').on('click',function(){

  var checkedSiteVal = isRadioChecked();

  if(!checkedSiteVal){
    jQuery.alert({
        title: 'Alert!',
        content: 'Please select a site from list!',
    });
    return false;
  }
  //$('#vehicleModal').modal('show'); 
  $('#hidden_site_id').val(checkedSiteVal);
  var url = "{{url('/')}}/sites/getAssignedVehicleAjax"; 
  
  $.ajax({
      type: "POST",
      url: url,      
      data: {
        "_token": $('meta[name="csrf-token"]').attr('content'),
        "site_id": checkedSiteVal            
      },
      success: function(res) {
          if((res.status) && (res.data.length > 0)){
            var html = '';
            jQuery.each(res.data, function(index, item) {
                html += '<tr>';
                html += "<td>"+item.project_name+"</td>";
                html += "<td>"+item.site_name+"</td>";                
                html += "<td>"+item.vehicle_type_name+"</td>";
                html += "<td>"+item.vehicle_name+"</td>";
                html += "<td>"+item.vehicle_number+"</td>";
                html += "<td>"+item.quantity+"</td>";
                html += "<td><span onclick='deleteSiteAssignment("+item.site_vehicle_id+",\"vehicle\")' style='color:red;cursor:pointer;' class='glyphicon glyphicon-trash'></span></td>";
                html += '</tr>';
                
            });
            $('#vehicle_modal_tr').html(html);
          }else{
            $('#vehicle_modal_tr').html('<td colspan="7" style="text-align:center;"><i>No vehicle assigned</i></td>');
          }          
          
          $('#vehicleModal').modal('show'); 
                    
      },
      error:function(request, status, error) {
          console.log("ajax call went wrong:" + request.responseText);
      }
  });
});

    

$('.site-sla-btn-action').on('click',function(){

    var checkedSiteVal = isRadioChecked();

    if(!checkedSiteVal){
      jQuery.alert({
          title: 'Alert!',
          content: 'Please select a site from list!',
      });
      return false;
    }
    //$('#vehicleModal').modal('show'); 
    $('#hidden_site_sla_id').val(checkedSiteVal);

    var url = "{{url('/')}}/sites/getAssignedSlaAjax"; 

    $.ajax({
        type: "POST",
        url: url,      
        data: {
          "_token": $('meta[name="csrf-token"]').attr('content'),
          "site_id": checkedSiteVal            
        },
        success: function(res) {
            if((res.status) && (res.data.length > 0)){
              var html = '';
              jQuery.each(res.data, function(index, item) {
                  var checked = '';
                  if(res.selected ==item.id){
                    checked = 'checked';
                  }
                  html += '<tr>';
                  html += "<td><input type='radio' name='sla_type' value='"+item.id+"' "+checked+"></td>";
                  html += "<td>"+item.name+"</td>";                                  
                  html += '</tr>';
                  
              });
              $('#site_sla_tr').html(html);
            }else{
              $('#site_sla_tr').html('<td colspan="2" style="text-align:center;"><i>No vehicle assigned</i></td>');
            }                  

            $('#siteSlaModal').modal('show'); 
                      
        },
        error:function(request, status, error) {
            console.log("ajax call went wrong:" + request.responseText);
        }
    });
  });




})

function deleteSiteAssignment(id,type){

  var url = "{{url('/')}}/sites/deleteSiteAssignment"; 

  $.ajax({
      type: "POST",
      url: url,      
      data: {
        "_token": $('meta[name="csrf-token"]').attr('content'),
        "id": id,"type":type            
      },
      success: function(res) {
          if(res.status){       
            if(type=="equipment"){
              
              $('.equipment-btn-action').trigger('click');
            }else if(type=="activity"){
              $('.activity-btn-action').trigger('click');
            }else if(type=="advance"){
              $('.site-advance-btn-action').trigger('click');
            }else if(type=="vehicle"){
              $('.vehicle-btn-action').trigger('click');
            }else if(type=="sitesla"){
              $('.site-sla-btn-action').trigger('click');
            }     
            
          }else{
            jQuery.alert({
                title: 'Alert!',
                content: 'Unable to delete',
            });
          }          
                    
      },
      error:function(request, status, error) {
          console.log("ajax call went wrong:" + request.responseText);
      }
  });
}

/**---------------Add vehicle code end--------------- */
</script>

