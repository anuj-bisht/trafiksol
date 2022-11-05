
<style type = "text/css">
    table tr td,table tr th{
		text-align: center;
        border:1px solid #000;
	}
	@media only screen and (max-width: 500px) {

	 table,head,tbody,th,td,tr {
		display: block;
        border:1px solid #000;
	}
	thead tr {
	display: none;
	}
	tr {
	 border: 1px solid #ccc;
	}
	td {
	border: none;
	border-bottom: 1px solid #eee;
	position: relative;
	padding-left: 50%;
	white-space: normal;
	text-align:left;
	min-height: 30px;
	overflow: hidden;
	word-break:break-all;
	}
	td:before {
	position: absolute;
	top: 6px;
	left: 6px;
	width: 45%;
	padding-right: 10px;
	text-align:left;
	font-weight: bold;
	}
	td:before { content: attr(data-title); }
}
</style>

	<div id="page-wrapper">
		<div class="container-fluid">


            <div class="row">
                    <div class="col-lg-10">
                        <h1 class="page-header">{{$site->name.'-'.date('y-m-d')}}</h1>
                    </div>
            </div>

            <div class="row">
                    <table cellpadding="0" style="width:100%;">
                        <tr>
                            <td style="padding-left:0px !important;text-align:left;border:0px;"><img src="{{$site->client->image}}" style="max-width:200px;"></td>
                            <td style="float:right;text-align:right;border:0px;"><img src="{{url('/')}}/images/trafikLogo.png?kk" style="max-width:180px;"></td>
                        </tr>

                    </table>

            </div>

            <div class="row">
                <div class="col-xs-3 col-sm-3 col-md-3">
                    <div class="form-group">
                        <strong>Name:</strong>
                        {{ $site->name }}
                    </div>
                </div>
                <div class="col-xs-3 col-sm-3 col-md-3">
                    <div class="form-group">
                        <strong>Client Name:</strong>
                        {{ $site->client->name }}
                    </div>
                </div>

                <div class="col-xs-3 col-sm-3 col-md-3">
                    <div class="form-group">
                        <strong>Prjoect alias:</strong>
                        {{ $site->alias_name }}
                    </div>
                </div>

                <div class="col-xs-3 col-sm-3 col-md-3">
                    <div class="form-group">
                        <strong>Location:</strong>
                        {{ $site->location }}
                    </div>
                </div>
                <div class="col-xs-3 col-sm-3 col-md-3">
                    <div class="form-group">
                        <strong>Address line1:</strong>
                        {{ $site->address1 }}
                    </div>
                </div>
                <div class="col-xs-3 col-sm-3 col-md-3">
                    <div class="form-group">
                        <strong>Address line2:</strong>
                        {{ $site->address2 }}
                    </div>
                </div>
                <div class="col-xs-3 col-sm-3 col-md-3">
                        <div class="form-group">
                            <strong>Stretch:</strong>
                            {{ $site->stretch_from }}-{{ $site->stretch_to }}
                        </div>
                </div>
                <div class="col-xs-3 col-sm-3 col-md-3">
                    <div class="form-group">
                        <strong>Country:</strong>
                        {{ $site->country->name }}
                    </div>
                </div>
                <div class="col-xs-3 col-sm-3 col-md-3">
                    <div class="form-group">
                        <strong>State:</strong>
                        {{ $site->state->name }}
                    </div>
                </div>
                <div class="col-xs-3 col-sm-3 col-md-3">
                    <div class="form-group">
                        <strong>City:</strong>
                        {{ $site->city }}
                    </div>
                </div>
                <div class="col-xs-3 col-sm-3 col-md-3">
                    <div class="form-group">
                        <strong>Zip:</strong>
                        {{ $site->zip }}
                    </div>
                </div>

            </div>


                <div class="row">
                        <div class="col-lg-10">
                            <h4>{{__('Site Users')}}</h1>
                        </div>
                </div>
                <table id="tableData" class="table-responsive table table-striped table-bordered" style="font-size:12px;width:100% !important">

                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>User Email</th>
                        </tr>
                    </thead>
                    <tbody>
                           @if($site_users->count())
                            @foreach($site_users as $value)
                            <tr>
                                <td>{{$value->name}}</td>
                                <td>{{$value->email}}</td>
                            </tr>
                            @endforeach
                           @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Username</th>
                            <th>User Email</th>
                        </tr>
                    </tfoot>
                </table>
                <div class="row">
                        <div class="col-lg-10">
                            <h4>{{__('Assigned Equipment')}}</h1>
                        </div>
                </div>
                <table id="tableData" class="table-responsive table table-striped table-bordered" style="font-size:12px;width:100% !important">

                    <thead>
                        <tr>
                            <th>Project Name</th>
                            <th>Site Name</th>
                            <th>Brand Name</th>
                            <th>Model Name</th>
                            <th>Equipment</th>
                            <th>Chainage</th>
                            <th>Location</th>
                            <th>Site SLA</th>
                        </tr>
                    </thead>
                    <tbody>
                           @if($equipmentAssigned->count())
                            @foreach($equipmentAssigned as $value)
                            <tr>
                                <td>{{$value->project_name}}</td>
                                <td>{{$value->site_name}}</td>
                                <td>{{$value->brand_name}}</td>
                                <td>{{$value->model_name}}</td>
                                <td>{{$value->equipment_name}}</td>
                                <td>{{$value->chainage}}</td>
                                <td>{{$value->equipment_location}}</td>
                                <td>{{$value->sla_name}}</td>
                            </tr>
                            @endforeach
                           @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Project Name</th>
                            <th>Site Name</th>
                            <th>Brand Name</th>
                            <th>Model Name</th>
                            <th>Equipment</th>
                            <th>Chainage</th>
                            <th>Location</th>
                            <th>Site SLA</th>
                        </tr>
                    </tfoot>
                </table>
                <div class="row">
                        <div class="col-lg-10">
                            <h4>{{__('Assigned Activity')}}</h1>
                        </div>
                </div>
                <table id="tableData" class="table-responsive table table-striped table-bordered" style="font-size:12px;width:100% !important">

                    <thead>
                        <tr>
                            <th>Project Name</th>
                            <th>Site Name</th>
                            <th>Activity Category</th>
                            <th>Activity Name</th>
                            <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                           @if($activityAssigned->count())
                            @foreach($activityAssigned as $value)
                            <tr>
                                <td>{{$value->project_name}}</td>
                                <td>{{$value->site_name}}</td>
                                <td>{{$value->activity_category_name}}</td>
                                <td>{{$value->activity_name}}</td>
                                <td>{{$value->quantity}}</td>
                            </tr>
                            @endforeach
                           @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Project Name</th>
                            <th>Site Name</th>
                            <th>Activity Category</th>
                            <th>Activity Name</th>
                            <th>Quantity</th>

                        </tr>
                    </tfoot>
                </table>
                <div class="row">
                        <div class="col-lg-10">
                            <h4>{{__('Assigned Vehicle')}}</h1>
                        </div>
                </div>
                <table id="tableData" class="table-responsive table table-striped table-bordered" style="font-size:12px;width:100% !important">

                    <thead>
                        <tr>
                            <th>Project Name</th>
                            <th>Site Name</th>
                            <th>Vehicle Type</th>
                            <th>Vehicle Name</th>
                            <th>Vehicle Number</th>
                            <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                           @if($vehicleAssigned->count())
                            @foreach($vehicleAssigned as $value)
                            <tr>
                                <td>{{$value->project_name}}</td>
                                <td>{{$value->site_name}}</td>
                                <td>{{$value->vehicle_type_name}}</td>
                                <td>{{$value->vehicle_name}}</td>
                                <td>{{$value->vehicle_number}}</td>
                                <td>{{$value->quantity}}</td>
                            </tr>
                            @endforeach
                           @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Project Name</th>
                            <th>Site Name</th>
                            <th>Vehicle Type</th>
                            <th>Vehicle Name</th>
                            <th>Vehicle Number</th>
                            <th>Quantity</th>

                        </tr>
                    </tfoot>
                </table>
                <div class="row">
                        <div class="col-lg-10">
                            <h4>{{__('Advance Taken')}}</h1>
                        </div>
                </div>
                <table id="tableData" class="table-responsive table table-striped table-bordered" style="font-size:12px;width:100% !important">

                    <thead>
                        <tr>
                            <th>Site Name</th>
                            <th>Given date</th>
                            <th>Given To</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                           @if($advance->count())
                            @foreach($advance as $value)
                            <tr>
                                <td>{{$value->site_name}}</td>
                                <td>{{$value->created_at}}</td>
                                <td>{{$value->username}}</td>
                                <td>{{$value->amount}}</td>
                            </tr>
                            @endforeach
                           @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Site Name</th>
                            <th>Given date</th>
                            <th>Given To</th>
                            <th>Amount</th>

                        </tr>
                    </tfoot>
                </table>



		</div>
	</div>
	<!-- /#page-wrapper -->
