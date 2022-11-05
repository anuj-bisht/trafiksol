@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">


      <div class="row">
				<div class="col-lg-12">
					<h1 class="page-header">{{__('Attendance List')}}</h1>
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
                  <th>Name</th>                      
                  <th>Attendance Marked</th> 
                  <th>Geo Point</th> 
                  <th>OS Type</th> 
                  <th>Image</th>                   
                  <th>status</th>
                  <th>Map</th>
              </tr>
          </thead>
          <tbody>
                        
          </tbody>
          <tfoot>
              <tr>                                                                            
                  <th>Name</th>                      
                  <th>Attendance Marked</th> 
                  <th>Geo Point</th> 
                  <th>OS Type</th> 
                  <th>Image</th>                   
                  <th>status</th>
                  <th>Map</th>
              </tr>
          </tfoot>
      </table>  


		</div>		
	</div>
	<!-- /#page-wrapper -->
 

  <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">Attendance Map</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12 modal_body_content">
              <p></p>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12 modal_body_map">
              <div class="location-map" id="location-map">
                <div style="width: 600px; height: 400px;" id="map_canvas"></div>
              </div>
            </div>
          </div>          
        </div>
      </div>
    </div>
</div>

<script src="//maps.googleapis.com/maps/api/js"></script>
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
              'url': '{{ url("/") }}/attendances/ajaxData',
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
                  'data': 'user_id',
                  'className': 'col-md-2',
                  'render': function(data,type,row){
                    
                    return row.user.name;
                  }
              },
              {
                  'data': 'mark_datetime',
                  'className': 'col-md-2',
                  'render': function(data,type,row){
                    
                    return row.mark_datetime;
                  }
              },
              {
                  'data': 'geo_point',
                  'className': 'col-md-2',
                  'orderable': false,
                  'render': function(data,type,row){
                    
                    return row.geo_point;
                  }
              },
              {
                  'data': 'ostype',
                  'className': 'col-md-2',
                  'render': function(data,type,row){
                    
                    return row.ostype;
                  }
              },
              {
                  'data': 'image',
                  'orderable': false,
                  'className': 'col-md-2',
                  'render': function(data,type,row){
                    
                    if(row.image){
                      return '<img src="'+row.image+'" with="50" height="50">';
                    }else{
                      return '';
                    }
                    
                  }
              },
              {
                  'data': 'entry_type',
                  'className': 'col-md-2',
                  'render': function(data,type,row){
                    
                    return row.entry_type;
                  }
              },
              {
                  'data': 'map',
                  'className': 'col-md-2',
                  'render': function(data,type,row){
                    var buttonHtml = '';
                    if(row.geo_point!=null){
                      var geoArr = row.geo_point.split(',');
                      var buttonHtml = '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal" data-lat="'+geoArr[0]+'" data-lng="'+geoArr[1]+'">Map</button>';
                    }
                    
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



          var map = null;
          var myMarker;
          var myLatlng;

          function initializeGMap(lat, lng) {
            myLatlng = new google.maps.LatLng(lat, lng);

            var myOptions = {
              zoom: 12,
              zoomControl: true,
              center: myLatlng,
              mapTypeId: google.maps.MapTypeId.ROADMAP
            };

            map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

            myMarker = new google.maps.Marker({
              position: myLatlng
            });
            myMarker.setMap(map);
          }

          // Re-init map before show modal
          $('#myModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            initializeGMap(button.data('lat'), button.data('lng'));
            $("#location-map").css("width", "100%");
            $("#map_canvas").css("width", "100%");
          });

          // Trigger map resize event after modal shown
          $('#myModal').on('shown.bs.modal', function() {
            google.maps.event.trigger(map, "resize");
            map.setCenter(myLatlng);
          });
  });




</script>


@endsection
