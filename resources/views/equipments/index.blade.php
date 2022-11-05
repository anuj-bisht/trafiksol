@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">


      <div class="row">
				<div class="col-lg-12">
					<h1 class="page-header">{{__('Equipment List')}}</h1>
				</div>
				<!-- /.col-lg-12 -->
			</div>  

      <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>&nbsp;</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-success" href="{{ route('equipments.create') }}"> Create New Equipment</a>
            </div>
        </div>
      </div>

      @include('layouts.flash')
      <div class="row">        
        <div class="col-lg-3">
         <select id="brandFilter" class="form-control">
              <option value="0">Select Brand Filter</option>
              @if(count($brands)>0)
                @foreach($brands as $v)
                  <option value="{{$v->id}}">{{$v->name}}</option>
                @endforeach
                @else
                @foreach($brands as $v)
                <option value="{{$v->id}}">{{$v->name}}</option>
              @endforeach
                
              @endif          
          </select>
        
        </div>

        <div class="col-lg-3">
          <select id="modelsFilter" class="form-control">
              <option value="0">Select Model Filter</option>
              @if(count($models)>0)
                @foreach($models as $v)
                  <option value="{{$v->id}}">{{$v->model}}</option>
                @endforeach
              @endif          
          </select>
        </div>
      </div>
      <br/>
      <table id="tableData" class="table-responsive table table-striped table-bordered" style="font-size:12px;width:100% !important">
          
          <thead>
          <tr>                   
            <th>Title</th>            
            <th>Brand</th>          
            <th>Model</th>                
            <th width="280px">Action</th>
          </tr>
          </thead>
          <tbody>
                        
          </tbody>
          <tfoot>
          <tr>            
              <th>Title</th>                
              <th>Brand</th>          
              <th>Model</th>                    
              <th width="280px">Action</th>
            </tr>
          </tfoot>
      </table>  


		</div>		
	</div>
	<!-- /#page-wrapper -->
  {!! Form::open(['method' => 'DELETE','route' => ['equipments.destroy', 1],'id'=>'deleteEquipment','style'=>'display:inline']) !!}
      
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
              'url': '{{ url("/") }}/equipments/ajaxData',
              'headers': {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
              },
              'type': 'post',
              'data': function(d) {
                //d.statusFilter = jQuery('#statusFilter').val();                
                d.brand = jQuery("#brandFilter option:selected").val();
                d.models = jQuery("#modelsFilter option:selected").val();
              },
            },          
            'columns': [              
              {
                  'data': 'title',
                  'className': 'col-md-2',
                  'render': function(data,type,row){
                    
                    return row.title;
                  }
              },
              {
                  'data': 'brand_id',
                  'className': 'col-md-2',
                  'render': function(data,type,row){
                    
                    return row.brand_name;
                  }
              },
              {
                  'data': 'model_id',
                  'className': 'col-md-2',
                  'render': function(data,type,row){                    
                    return row.model_name;
                  }
              },            
              {
                'data': 'Action',
                'className': 'col-md-2',
                'orderable': false,
                'render': function(data, type, row) {
                  var buttonHtml = '<a class="btn btn-primary" href="'+url+'/equipments/'+row.id+'/edit">Edit</a>&nbsp;&nbsp;<a class="btn btn-danger" href="javascript:void(0);" onclick="deleteData('+row.id+')">Delete</a>';
                  return buttonHtml;
                }
              }
            ]
          });   
     
              
      
    $('#brandFilter').change(function () {	    
      table.draw();
    });      
    $('#modelsFilter').change(function () {	    
      table.draw();
    });      

  });

function deleteData(id){

  $.confirm({
      title: 'Confirm!',
      content: 'Are you sure want to delete?',
      buttons: {
          confirm: function () {
            $('#deleteEquipment').attr('action', url+"/equipments/"+id).submit();  
          },
          cancel: function () {
              return true;
          }
      }
  });

}        
 
</script>

@endsection
