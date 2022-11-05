@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">


            <div class="row">
                    <div class="col-lg-10">
                        <h1 class="page-header">{{__('Edit Brand')}}</h1>                        
                    </div>		                    
            </div>

            @include('layouts.flash')

            {!! Form::model($brand, ['method' => 'PATCH','route' => ['brands.update', $brand->id]]) !!}            
            <div class="row">

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Vendors:</strong>
                        <select name="vendor_id" class="form-control">
                            <option value="">Main-Category</option>
                            @if($vendors->count() > 0 )
                               @foreach($vendors as $v)
                                    <option value="{{$v->id}}" @if($v->id==$brand->vendor_id) selected @endif>{{$v->name}}</option>
                               @endforeach     
                            @endif
                        <select>                        
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Name:</strong>
                        {!! Form::text('name', null, array('placeholder' => 'Brand Name','class' => 'form-control')) !!}
                    </div>
                </div>
                                
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <a class="btn btn-primary" href="{{ route('brands.index') }}"> Back</a>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
            {!! Form::close() !!}
                        


		</div>		
	</div>
	<!-- /#page-wrapper -->
            
@endsection
