@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">


            <div class="row">
                    <div class="col-lg-10">
                        <h1 class="page-header">{{__('Tomorrow DPR Activity')}}</h1>                        
                    </div>		                    
            </div>                      
            
            <div class="row">
				<div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Username:</strong>
                        {{ $data->username }}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Site name:</strong>
                        {{ $data->site_name }}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Activity Category:</strong>
                        {{ $data->activity_category_name }}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Activity Name:</strong>
                        {{ $data->activity_name }}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Site name:</strong>
                        {{ $data->site_name }}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>UOM:</strong>
                        {{ $data->uom_name }}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Quantity:</strong>
                        {{ $data->quantity }}
                    </div>
                </div>
                
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Description:</strong>
                        {{ $data->description }}
                    </div>
                </div>
                
                
                
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Created Date:</strong>
                        {{ $data->created_at }}
                    </div>
                </div>
                
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <a class="btn btn-primary" href="{{ url('/') }}/activities/activitytomorrow"> Back</a>
                </div>                
            </div>
                        
                        


		</div>		
	</div>
	<!-- /#page-wrapper -->
            
@endsection
