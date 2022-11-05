@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

        
	<div id="page-wrapper">
		<div class="container-fluid">


            <div class="row">
                    <div class="col-lg-10">
                        <h1 class="page-header">{{__('Create New Equipment')}}</h1>                        
                    </div>		                    
            </div>

            @include('layouts.flash')
            
            {!! Form::open(array('route' => 'equipments.store','method'=>'POST')) !!}
            <div class="row">
                
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Title:</strong>
                        {!! Form::text('title', null, array('placeholder' => 'Title','class' => 'form-control')) !!}
                    </div>
                </div>
               
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Vendors:</strong>
                        {!! Form::select('vendor_id', $vendors,[], array('class' => 'form-control','id'=>'data_vendor_id','placeholder' => 'Please Select Vendor')) !!}
                    </div>
                </div>
               
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Brand:</strong>
                        {!! Form::select('brand_id', [],[], array('class' => 'form-control','placeholder' => 'Please Select','id'=>'main_brand_id')) !!}
                    </div>
                </div>
               

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Model Name:</strong>
                        {!! Form::select('model_id', [],[], array('class' => 'form-control','id'=>'model_id')) !!}
                    </div>
                </div>
                
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>UOM:</strong>
                        {!! Form::select('uom_id', $uom,[], array('class' => 'form-control','id'=>'uom_id')) !!}
                    </div>
                </div>

                
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <a class="btn btn-primary" href="{{ route('equipments.index') }}"> Back</a>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
            {!! Form::close() !!}
                        


		</div>		
	</div>
	<!-- /#page-wrapper -->
    <script>


</script>

@endsection

