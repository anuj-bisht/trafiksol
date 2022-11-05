@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

        
	<div id="page-wrapper">
		<div class="container-fluid">


            <div class="row">
                    <div class="col-lg-10">
                        <h1 class="page-header">{{__('Create New Model')}}</h1>                        
                    </div>		                    
            </div>

            @include('layouts.flash')
            
            {!! Form::open(array('route' => 'models.store','method'=>'POST')) !!}
            <div class="row">
                

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Brand:</strong>
                        {!! Form::select('brand_id', $brand,[], array('class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Model Name:</strong>
                        {!! Form::text('model', null, array('placeholder' => 'Model Name','class' => 'form-control')) !!}
                    </div>
                </div>

                <!-- <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Make:</strong>
                        <select name="make" class="form-control">
                            {{ $oldyear= date('Y')-30 }}
                            {{ $nowyear = date('Y') }}

                            @for ($i = $oldyear ; $i <=  $nowyear ; $i++)
                            <option value="{{ $i }}">{{ $i }} Year </option>
                            @endfor               
                        </select>                        
                    </div>
                </div> -->

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Build:</strong>
                        {!! Form::text('build', null, array('placeholder' => 'Build Name','class' => 'form-control')) !!}
                    </div>
                </div>
                
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <a class="btn btn-primary" href="{{ route('models.index') }}"> Back</a>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
            {!! Form::close() !!}
                        


		</div>		
	</div>
	<!-- /#page-wrapper -->
            
@endsection
