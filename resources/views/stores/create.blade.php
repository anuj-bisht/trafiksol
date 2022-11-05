@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

        
	<div id="page-wrapper">
		<div class="container-fluid">


            <div class="row">
                    <div class="col-lg-10">
                        <h1 class="page-header">{{__('Create New Store')}}</h1>                        
                    </div>		                    
            </div>

            @include('layouts.flash')
            
            {!! Form::open(array('route' => 'stores.store','method'=>'POST')) !!}
            <div class="row">
                

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Item Name:</strong>
                        {!! Form::text('item_name', null, array('placeholder' => 'Item Name','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Item code:</strong>
                        {!! Form::text('item_code', null, array('placeholder' => 'Item Code','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Docket No:</strong>
                        {!! Form::text('docket_no', null, array('placeholder' => 'Docket no','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Site:</strong>
                        {!! Form::select('site_id', $sites,[], array('class' => 'form-control','placeholder' => 'Please Select','id'=>'site_id')) !!}
                    </div>
                </div>
                
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Brand:</strong>
                        {!! Form::select('brand_id', $brand,[], array('class' => 'form-control','placeholder' => 'Please Select','id'=>'main_brand_id')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Model Name:</strong>
                        {!! Form::select('model_id', [],[], array('class' => 'form-control','id'=>'model_id','placeholder'=>'Select Model')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Equipment:</strong>
                        {!! Form::select('equipment_id', [],[], array('class' => 'form-control','id'=>'equipment_id','placeholder'=>'Select Equipment')) !!}
                    </div>
                </div>
                
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Quantity:</strong>
                        {!! Form::text('quantity', null, array('placeholder' => 'Quantity','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Item Type:</strong>
                        {!! Form::select('store_type', ["Store"=>"Store Item","Return"=>"Return Item"],null, array('placeholder' => 'Item Type','class' => 'form-control')) !!}
                    </div>
                </div>
                
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <a class="btn btn-primary" href="{{ route('stores.index') }}"> Back</a>
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

