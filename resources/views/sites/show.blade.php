@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">


            <div class="row">
                    <div class="col-lg-10">
                        <h1 class="page-header">{{__('Show Site')}}</h1>
                    </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <a class="btn btn-primary pull-right" href="{{ route('sites.index') }}"> Back</a>
            </div>
            <a href="{{url('/')}}/sites/print/{{$site->id}}" class="btn btn-danger">Download PDF</a>
            <br/><br/><br/><br/>
            @include('layouts.flash')
            <div id="content">
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
                            {{ $site->stretch_from }} - {{ $site->stretch_to }}
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
                                <th>Phone</th>
                                <th>User Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($site_users->count())
                                @foreach($site_users as $value)
                                <tr>
                                    <td>{{$value->name}}</td>
                                    <td>{{$value->email}}</td>
                                    <td>{{$value->phone}}</td>
                                    <td>{{$value->role_name}}</td>
                                <tr>
                                @endforeach
                            @endif
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Username</th>
                                <th>User Email</th>
                                <th>Phone</th>
                                <th>User Role</th>
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
                                <tr>
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
                                <tr>
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
                                <tr>
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
                                <tr>
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

                </div> <!--content close-->

		</div>
	</div>
	<!-- /#page-wrapper -->
<script src="https://code.jquery.com/jquery-1.12.3.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/0.9.0rc1/jspdf.min.js"></script>
<script>

var doc = new jsPDF();
var specialElementHandlers = {
    '#editor': function (element, renderer) {
        return true;
    }
};

$('#cmd').click(function () {
    doc.fromHTML($('#content').html(), 15, 15, {
        'width': 170,
            'elementHandlers': specialElementHandlers
    });
    doc.save('site-detail.pdf');
});


</script>

@endsection
