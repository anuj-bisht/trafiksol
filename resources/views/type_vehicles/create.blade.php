@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">


            <div class="row">
                    <div class="col-lg-10">
                        <h1 class="page-header">{{__('Create New Vehicle Type')}}</h1>                        
                    </div>		                    
            </div>

            @include('layouts.flash')
            
            {!! Form::open(array('route' => 'type_vehicles.store','method'=>'POST')) !!}
            <div class="row">
                
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Type:</strong>
                        {!! Form::text('name', null, array('placeholder' => 'Vehicle Type','class' => 'form-control')) !!}
                    </div>
                </div>                
                
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <a class="btn btn-primary" href="{{ route('type_vehicles.index') }}"> Back</a>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
            {!! Form::close() !!}
                        


		</div>		
	</div>
	<!-- /#page-wrapper -->
            
@endsection
