@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')
<style>
.panel-blue{
	border-color: #999;
}

.panel-blue >.panel-heading {
    color: #fff;
    background-color: #999;
    border-color: #999;
}
.panel-blue a {
    color: #999;
}
</style>
	<div id="page-wrapper">
		<div class="container-fluid">
			<div class="row">
				<div class="col-lg-12">
					<h1 class="page-header">Dashboard</h1>
				</div>
				<!-- /.col-lg-12 -->
			</div>
      
			<!-- /.row -->
      <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
      <script type="text/javascript">
        google.charts.load("current", {packages:["corechart"]});
        google.charts.setOnLoadCallback(drawChart);
        function drawChart() {
          var data = google.visualization.arrayToDataTable([
            ['Task', 'Hours per Day'],
            ['Closed Ticket in Correct Time', {{$closeticketincorrecttime}}],
	    ['Closure requests', {{$closurerequest}}],
             <?php echo $ticket_status ?>,
            ['No. of overdue closed tickets', {{$overdueclosedtickets}}],
             ['No. of overdue opened tickets', {{$overdueopentickets}}],

            
           
           
            
          ]);
           
          
           var options = {
            title: 'Count of total calls generated',
            colors: ['#42f548','#017807','#7d967e','#f5d08c','red', '#90ee90'],
            is3D: true,
          };
          console.log(data);
          var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));
          chart.draw(data, options);
        }
      </script>
     
         
        
			<div class="row">

                <div class="col-lg-2 col-md-6">
                    <label for="">Tickets</label>
                </div>
                <div class="col-lg-4 col-md-6">
                    <label for="">Sites</label>
                    <select name="sites" id="sites" class="form-control">
                        <option value="All">All</option>
                        @if(!$sites->isEmpty())
                            @foreach($sites as $value)
                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label for="">From Date</label>
                    <input type="date" id="fromDate" class="form-control">
                </div>
                <div class="col-lg-2 col-md-6">
                    <label for="">To Date</label>
                    <input type="date" id="toDate" class="form-control">
                </div>
                <div class="col-lg-2 col-md-6">
                    <label for=""></label>
                    <button class="btn btn" onclick="getAllData()">Search</button>
                </div>
            </div>
            <br>
            <div class="row">
				<div class="col-lg-3 col-md-6">
					<div class="panel panel-primary">
						<div class="panel-heading">
							<div class="row">
								<div class="col-xs-3">
									<i class="fa fa-comments fa-5x"></i>
								</div>
								<div class="col-xs-9 text-right">
									<div class="huge counter" id="totalTickets"><img src="{{asset('/images/loading-36.gif')}}" style="width: 25%;"></div>
									<div>Today Tickets</div>
								</div>
							</div>
						</div>
						<a href="#" >
							<div class="panel-footer" data-toggle="modal" data-target="#exampleModalCenter">
								<span class="pull-left"><a >View Details</a></span>

								<div class="clearfix"></div>
							</div>
						</a>



					</div>
				</div>
				<div class="col-lg-3 col-md-6">
					<div class="panel " style="background-color: red;">
						<div class="panel-heading">
							<div class="row">
								<div class="col-xs-3">
									<i class="fa fa-tasks fa-5x"></i>
								</div>
								<div class="col-xs-9 text-right">
									<div class="huge counter" id="openTickets"><img src="{{asset('/images/loading-36.gif')}}" style="width: 25%;"></div>
									<div>Open Tickets</div>
								</div>
							</div>
						</div>
						<a href="#">
							<div class="panel-footer" data-toggle="modal" data-target="#openTicketModel">
								<span class="pull-left">View Details</span>
								<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>

								<div class="clearfix"></div>
							</div>
						</a>
					</div>
				</div>
				<div class="col-lg-3 col-md-6">
					<div class="panel " style="background-color: rgb(144, 238, 144);">
						<div class="panel-heading">
							<div class="row">
								<div class="col-xs-3">
									<i class="fa fa-shopping-cart fa-5x"></i>
								</div>
								<div class="col-xs-9 text-right" >
									<div class="huge counter" id="closeTickets"><img src="{{asset('/images/loading-36.gif')}}" style="width: 25%;"></div>
									<div>Closed Tickets</div>
								</div>
							</div>
						</div>
						<a href="#">
							<div class="panel-footer" data-toggle="modal" data-target="#closeTicketModel">
								<span class="pull-left">View Details</span>
								<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>

								<div class="clearfix"></div>
							</div>
						</a>
					</div>
				</div>
				<div class="col-lg-3 col-md-6">
					<div class="panel " style="background-color: rgb(239, 134, 95);">
						<div class="panel-heading">
							<div class="row">
								<div class="col-xs-3">
									<i class="fa fa-support fa-5x"></i>
								</div>
								<div class="col-xs-9 text-right">
									<div class="huge counter" id="remHrTickets "><img src="{{asset('/images/loading-36.gif')}}" style="width: 25%;"></div>
									<div>Overdue Closed Tickets</div>
								</div>
							</div>
						</div>
						<a href="#">
							<div class="panel-footer" data-toggle="modal" data-target="#overdueTicketModel">
								<span class="pull-left">View Details</span>
								<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>

								<div class="clearfix"></div>
							</div>
						</a>
					</div>
				</div>
				<div class="col-lg-3 col-md-6">
					<div class="panel panel-blue">
						<div class="panel-heading">
							<div class="row">
								<div class="col-xs-3">
									<i class="fa fa-ticket fa-5x"></i>
								</div>
								<div class="col-xs-9 text-right">
									<div class="huge counter" id="pausedTicket"><img src="{{asset('/images/loading-36.gif')}}" style="width: 25%;"></div>
									<div>Ticket Pause</div>
								</div>
							</div>
						</div>
						<a href="#">
							<div class="panel-footer" data-toggle="modal" data-target="#pausedTicketModel">
								<span class="pull-left">View Details</span>
								<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>

								<div class="clearfix"></div>
							</div>
						</a>
					</div>
				</div>
                <div class="col-lg-3 col-md-6">
					<div class="panel panel-blue">
						<div class="panel-heading">
							<div class="row">
								<div class="col-xs-3">
									<i class="fa fa-ticket fa-5x"></i>
								</div>
								<div class="col-xs-9 text-right">
									<div class="huge counter" id="hardware_req"><img src="{{asset('/images/loading-36.gif')}}" style="width: 25%;"></div>
									<div>Hardware Request</div>
								</div>
							</div>
						</div>
						<a href="#">
							<div class="panel-footer" data-toggle="modal" data-target="#hardwareReqTicketModel">
								<span class="pull-left">View Details</span>
								<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>

								<div class="clearfix"></div>
							</div>
						</a>
					</div>
				</div>
			</div>
			<!-- /.row -->
			<div class="row">
        <div class="container">
          <div class="row">
            <div class="col-sm-12 ">
        <div  id="piechart_3d" style="width:100%; height: 650px; "  ></div>
       
         </div>
        </div>
         </div>
				{{-- <div class="col-lg-12">
                    <table id="tableData" class="table-responsive table table-striped table-bordered" style="font-size:12px;width:100% !important">

                        <thead>
                            <tr>

                                <th>Subject</th>
                                <th>Description</th>
                                <th>Priority</th>
                                <th>Stretch Points</th>
                                <th>Status</th>


                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot>
                            <tr>

                                <th>Subject</th>
                                <th>Description</th>
                                <th>Priority</th>
                                <th>Stretch Points</th>
                                <th>Status</th>

                            </tr>
                        </tfoot>
                    </table>
						{{-- <h2 style="text-align:center;color:gray">{{ Auth::user()->name }} {{ __('welcome to TrafikSol portal') }}</h2> --}}
				{{--</div>
                <div class="col-lg-12">
                    <table id="openTicketData" class="table-responsive table table-striped table-bordered" style="font-size:12px;width:100% !important">

                        <thead>
                            <tr>

                                <th>Subject</th>
                                <th>Description</th>
                                <th>Priority</th>
                                <th>Stretch Points</th>
                                <th>Status</th>


                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot>
                            <tr>

                                <th>Subject</th>
                                <th>Description</th>
                                <th>Priority</th>
                                <th>Stretch Points</th>
                                <th>Status</th>

                            </tr>
                        </tfoot>
                    </table>

				</div> --}}
			</div>
			<!-- /.row -->
		</div>
		<!-- /.container-fluid -->
	</div>
  
  
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog-lg modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLongTitle">Today Tickets</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <div class="col-lg-12">
                    <table id="" class="table-responsive table table-striped table-bordered" style="font-size:12px;width:100% !important">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Description</th>
                                <th>Priority</th>
                                <th>Stretch Points</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="ticketData">

                        </tbody>
                        <tfoot>
                            <tr>

                                <th>Subject</th>
                                <th>Description</th>
                                <th>Priority</th>
                                <th>Stretch Points</th>
                                <th>Status</th>

                            </tr>
                        </tfoot>
                    </table>
						{{-- <h2 style="text-align:center;color:gray">{{ Auth::user()->name }} {{ __('welcome to TrafikSol portal') }}</h2> --}}
				</div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

            </div>
          </div>
        </div>
      </div>
      <div class="modal fade" id="openTicketModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog-lg modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLongTitle">Open Tickets</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <div class="col-lg-12">
                    <table id="" class="table-responsive table table-striped table-bordered" style="font-size:12px;width:100% !important">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Description</th>
                                <th>Priority</th>
                                <th>Stretch Points</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="openticketData">

                        </tbody>
                        <tfoot>
                            <tr>

                                <th>Subject</th>
                                <th>Description</th>
                                <th>Priority</th>
                                <th>Stretch Points</th>
                                <th>Status</th>

                            </tr>
                        </tfoot>
                    </table>
						{{-- <h2 style="text-align:center;color:gray">{{ Auth::user()->name }} {{ __('welcome to TrafikSol portal') }}</h2> --}}
				</div>
            </div>
            <div class="modal-footer">
              {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> --}}

            </div>
          </div>
        </div>
      </div>
      <div class="modal fade" id="closeTicketModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog-lg modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLongTitle">Closed Tickets</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <div class="col-lg-12">
                    <table id="" class="table-responsive table table-striped table-bordered" style="font-size:12px;width:100% !important">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Description</th>
                                <th>Priority</th>
                                <th>Stretch Points</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="closeticketData">

                        </tbody>
                        <tfoot>
                            <tr>

                                <th>Subject</th>
                                <th>Description</th>
                                <th>Priority</th>
                                <th>Stretch Points</th>
                                <th>Status</th>

                            </tr>
                        </tfoot>
                    </table>
						{{-- <h2 style="text-align:center;color:gray">{{ Auth::user()->name }} {{ __('welcome to TrafikSol portal') }}</h2> --}}
				</div>
            </div>
            <div class="modal-footer">
              {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> --}}

            </div>
          </div>
        </div>
      </div>
      <div class="modal fade" id="overdueTicketModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog-lg modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLongTitle">Overdue Tickets</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <div class="col-lg-12">
                    <table id="" class="table-responsive table table-striped table-bordered" style="font-size:12px;width:100% !important">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Description</th>
                                <th>Priority</th>
                                <th>Stretch Points</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="overdueticketData">

                        </tbody>
                        <tfoot>
                            <tr>

                                <th>Subject</th>
                                <th>Description</th>
                                <th>Priority</th>
                                <th>Stretch Points</th>
                                <th>Status</th>

                            </tr>
                        </tfoot>
                    </table>
						{{-- <h2 style="text-align:center;color:gray">{{ Auth::user()->name }} {{ __('welcome to TrafikSol portal') }}</h2> --}}
				</div>
            </div>
            <div class="modal-footer">
              {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> --}}

            </div>
          </div>
        </div>
      </div>
      <div class="modal fade" id="hardwareReqTicketModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog-lg modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLongTitle">Hardware Requests</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <div class="col-lg-12">
                    <table id="" class="table-responsive table table-striped table-bordered" style="font-size:12px;width:100% !important">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Description</th>
                                <th>Priority</th>
                                <th>Stretch Points</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="hardwareReqticketData">

                        </tbody>
                        <tfoot>
                            <tr>

                                <th>Subject</th>
                                <th>Description</th>
                                <th>Priority</th>
                                <th>Stretch Points</th>
                                <th>Status</th>

                            </tr>
                        </tfoot>
                    </table>
						{{-- <h2 style="text-align:center;color:gray">{{ Auth::user()->name }} {{ __('welcome to TrafikSol portal') }}</h2> --}}
				</div>
            </div>
            <div class="modal-footer">
              {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> --}}

            </div>
          </div>
        </div>
      </div>
     
     
      <div class="modal fade" id="pausedTicketModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog-lg modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLongTitle">Paused Tickets</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <div class="col-lg-12">
                    <table id="" class="table-responsive table table-striped table-bordered" style="font-size:12px;width:100% !important">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Description</th>
                                <th>Priority</th>
                                <th>Stretch Points</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="pausedticketData">

                        </tbody>
                        <tfoot>
                            <tr>

                                <th>Subject</th>
                                <th>Description</th>
                                <th>Priority</th>
                                <th>Stretch Points</th>
                                <th>Status</th>

                            </tr>
                        </tfoot>
                    </table>
						{{-- <h2 style="text-align:center;color:gray">{{ Auth::user()->name }} {{ __('welcome to TrafikSol portal') }}</h2> --}}
				</div>
            </div>
            <div class="modal-footer">
              {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> --}}

            </div>
          </div>
        </div>
      </div>
      
      
      
	<!-- /#page-wrapper -->
<script>

    function getAllData()
    {
        var sites = document.getElementById('sites').value;
        var fromDate = jQuery('#fromDate').val();
        var toDate = jQuery('#toDate').val();

        jQuery.ajax({
            url:"{{ url('/dashboard/getTotalDataCount') }}",
            type:"POST",
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
              },
            data:{sites:sites,fromDate:fromDate,toDate:toDate},
            success:function(response)
            {
                if(response.status == 200)
                {
                    jQuery('#totalTickets').html(response.todayTickets);
                    var openTickets = 0;
                    var closeTickets = 0;
                    var remHrTickets = 0;
                    var pausedTicket = 0;
                    var hardware_req = 0;
                    var ticktdata = "";
                    var openTicketData = "";
                    var closedTicketData ="";
                    var overdueTicketData = "";
                    var pausedTicketData
                    var hardwareReqTicketData
                    jQuery.each(response.tickets,function(index,el){

                      if(el.status == 'open')
                      {
                        openTicketData += '<tr><td>'+el.subject+'</td><td>'+el.description+'</td><td>'+el.priority+'</td><td>'+el.stretch_point+'</td><td>'+el.status+'</td></tr>';
                        openTickets++;
                      }
                      if(el.status == 'close')
                      {
                        
                        
                        if(el.close_time <"0:0")
                        {
                          overdueTicketData += '<tr><td>'+el.subject+'</td><td>'+el.description+'</td><td>'+el.priority+'</td><td>'+el.stretch_point+'</td><td>'+el.status+'</td></tr>';
                          remHrTickets++;
                        }
                        else{
                          closedTicketData += '<tr><td>'+el.subject+'</td><td>'+el.description+'</td><td>'+el.priority+'</td><td>'+el.stretch_point+'</td><td>'+el.status+'</td></tr>';

                        closeTickets++;
                        }
                      }
                      
                      if(el.ticket_pauses == "Yes")
                      {
                          pausedTicketData += '<tr><td>'+el.subject+'</td><td>'+el.description+'</td><td>'+el.priority+'</td><td>'+el.stretch_point+'</td><td>'+el.status+'</td></tr>';
                        pausedTicket++;
                      }
                      if(el.hardwareReq == "Yes")
                      {
                          hardwareReqTicketData += '<tr><td>'+el.subject+'</td><td>'+el.description+'</td><td>'+el.priority+'</td><td>'+el.stretch_point+'</td><td>'+el.status+'</td></tr>';
                          hardware_req++;
                      }
                      ticktdata += '<tr><td>'+el.subject+'</td><td>'+el.description+'</td><td>'+el.priority+'</td><td>'+el.stretch_point+'</td><td>'+el.status+'</td></tr>';
                    });
                    jQuery('#ticketData').html(ticktdata);
                    jQuery('#openTickets').html(openTickets);
                    jQuery('#closeTickets').html(closeTickets);
                    jQuery('#remHrTickets').html(remHrTickets);
                    jQuery('#pausedTicket').html(pausedTicket);
                    jQuery('#hardware_req').html(hardware_req);

                    jQuery('#openticketData').html(openTicketData);
                    jQuery('#closeticketData').html(closedTicketData);
                    // console.log(overdueTicketData);
                    jQuery('#overdueticketData').html(overdueTicketData);
                    jQuery('#pausedticketData').html(pausedTicketData);
                    jQuery('#hardwareReqticketData').html(hardwareReqTicketData);

                }
            }

        });
    }
      var url = "{{url('/')}}";
    var table = '';
    var seconds = 0;

    jQuery(document).ready(function() {
        getAllData();


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
              'url': '{{ url("/dashboard/getTodayTickets") }}',
              'headers': {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
              },
              'type': 'get',
              'data': function(d) {
                // alert('hi');
                //d.hourFilter = jQuery("#hourFilter option:selected").val();
              },
            },

            'columns': [

              {
                  'data': 'subject',
                  'className': 'col-md-2',
                  'render': function(data,type,row){

                    return row.subject;
                  }
              },{
                  'data': 'description',
                  'className': 'col-md-2',
                  'render': function(data,type,row){

                    return row.description;
                  }
              },{
                  'data': 'priority',
                  'className': 'col-md-2',
                  'render': function(data,type,row){

                    return row.priority;
                  }
              },{
                  'data': 'Stretch',
                  'className': 'col-md-2',
                  'render': function(data,type,row){

                    return row.stretch_point;
                  }
              },{
                  'data': 'Status',
                  'className': 'col-md-2',
                  'render': function(data,type,row){

                    return row.status;
                  }
              }
            ]
          });
          table = jQuery('#openTicketData').DataTable({
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
              'url': '{{ url("/dashboard/getTodayTickets") }}',
              'headers': {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
              },
              'type': 'get',
              'data': function(d) {
                // alert('hi');
                //d.hourFilter = jQuery("#hourFilter option:selected").val();
              },
            },

            'columns': [

              {
                  'data': 'subject',
                  'className': 'col-md-2',
                  'render': function(data,type,row){

                    return row.subject;
                  }
              },{
                  'data': 'description',
                  'className': 'col-md-2',
                  'render': function(data,type,row){

                    return row.description;
                  }
              },{
                  'data': 'priority',
                  'className': 'col-md-2',
                  'render': function(data,type,row){

                    return row.priority;
                  }
              },{
                  'data': 'Stretch',
                  'className': 'col-md-2',
                  'render': function(data,type,row){

                    return row.stretch_point;
                  }
              },{
                  'data': 'Status',
                  'className': 'col-md-2',
                  'render': function(data,type,row){

                    return row.status;
                  }
              }
            ]
          });


});


</script>
@endsection
