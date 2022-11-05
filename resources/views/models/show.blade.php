@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">


            <div class="row">
                    <div class="col-lg-10">
                        <h1 class="page-header">{{__('Show Brand')}}</h1>                        
                    </div>		                    
            </div>
            
            @include('layouts.flash')
            
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Brand Name:</strong>
                        {{ $model->brand->name }}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Model Name:</strong>
                        {{ $model->model }}
                    </div>
                </div>

                <!-- <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Make:</strong>
                        {{ $model->make }}
                    </div>
                </div> -->

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Build:</strong>
                        {{ $model->build }}
                    </div>
                </div>
                
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <a class="btn btn-primary" href="{{ route('models.index') }}"> Back</a>
                </div>                
            </div>
                        
                        


		</div>		
	</div>
	<!-- /#page-wrapper -->
            
@endsection