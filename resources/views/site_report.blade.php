@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">


      <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Site Report</h2>
            </div>
        </div>
      </div>

      @include('layouts.flash')
      
      
      <table id="tableData" class="table-responsive table table-striped table-bordered" style="font-size:12px;width:100% !important">
          
          <thead>
              <tr>
                  <th>Site name</th>                                                                            
                  <th>Report Date</th>                                           
                  <th>Action</th> 
              </tr>
          </thead>
          <tbody>
                        
          </tbody>
          <tfoot>
              <tr>                                                                            
                  <th>Site name</th>                                                                            
                  <th>Report Date</th>                                           
                  <th>Action</th> 
              </tr>
          </tfoot>
      </table>  


		</div>		
	</div>
	<!-- /#page-wrapper -->
    <form  method="POST" id="report-form-submit">
        {{csrf_field()}}
        <input type="hidden" name="download" value="0">
        <input type="hidden" name="id" value="">        
    </form>

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
                columns: [0, 1]//"thead th:not(.noExport)"
              },
              className: 'btn btn-default',
                init: function(api, node, config) {
                  $(node).removeClass('dt-button')
                },
            },
            {extend: 'pdfHtml5',
              exportOptions: {
                columns: [0, 1] //"thead th:not(.noExport)"
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
              'url': '{{ url("/") }}/reportAjax',
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
                  'data': 'sitename',
                  'className': 'col-md-5',
                  'render': function(data,type,row){
                    
                    return row.name;
                  }
              },
              {
                  'data': 'report_date',
                  'className': 'col-md-4',
                  'render': function(data,type,row){
                    
                    return row.created_at;
                  }
              },            
              {
                'data': 'Action',
                'orderable': false,
                'className': 'col-md-3',
                'render': function(data, type, row) {
                   
                  var buttonHtml = '<button class="btn btn-info" onclick="reportAction('+row.id+',\'view\')">View</button>&nbsp;&nbsp;<button class="btn btn-danger" onclick="reportAction('+row.id+',\'download\')">Download</button>&nbsp;&nbsp;<button class="btn btn-success" onclick="reportAction('+row.id+',\'send\')">Send</button>';
                  return buttonHtml;
                }
              }
            ]
          });   
     
              
       
    

  });

function reportAction(site_id,action){
    var url = "{{url('/')}}";
    var frm = $('#report-form-submit');
    frm.attr('action', url+'/generateReportPDF/'+site_id);                
    if(action=='view'){
        window.open(url+'/generateReportPDF/'+site_id);
    }else if(action=='download'){                
        $('input[name="download"]').val(1);        
        $('input[name="id"]').val(site_id);
        frm.trigger('submit');
    }else{        
        $('input[name="download"]').val(0);
        $('input[name="id"]').val(site_id);
        //$('#report-form-submit').trigger('submit');

        $.ajax({
            type: "POST",
            url: url+'/generateReportPDF/'+site_id,
            data: frm.serialize(),           
            success: function(res) {
                if(res.status){
                    jQuery.alert({
                        title: 'Success',
                        content: 'Report send successfully',
                    });    
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
    }
}

</script>

@endsection
