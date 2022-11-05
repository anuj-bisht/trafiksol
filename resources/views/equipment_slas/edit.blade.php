@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">


            <div class="row">
                    <div class="col-lg-10">
                        <h1 class="page-header">{{__('Edit SLA Category')}}</h1>                        
                    </div>		                    
            </div>

            @include('layouts.flash')

            {!! Form::model($equipment_sla, ['method' => 'PATCH','route' => ['equipment_slas.update', $equipment_sla->id]]) !!}            
            <div class="row">

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Name:</strong>
                        {!! Form::text('name', null, array('placeholder' => 'Category Name','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Service Type:</strong>
                        {!! Form::text('hours_allocated', null, array('placeholder' => 'Service','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Sla Type:</strong>
                        {!! Form::select('sla_type', ['site'=>'Site','equipment'=>'Equipment','ticket'=>'Ticket','store'=>'Store'],$equipment_sla->sla_type, array('placeholder' => 'Sla Type','class' => 'form-control')) !!}
                    </div>
                </div>
                                
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <a class="btn btn-primary" href="{{ route('equipment_slas.index') }}"> Back</a>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
            {!! Form::close() !!}
                        


		</div>		
	</div>
	<!-- /#page-wrapper -->
            
@endsection
