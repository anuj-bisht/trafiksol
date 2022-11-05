@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">


            <div class="row">
                    <div class="col-lg-10">
                        <h1 class="page-header">{{__('Create New Activity')}}</h1>                        
                    </div>		                    
            </div>

            @include('layouts.flash')
            
            {!! Form::open(array('route' => 'activities.store','method'=>'POST')) !!}
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Activity Category:</strong>
                        <select name="main_category" id="main_activity_category" class="form-control">
                            <option value="0">Activity-Category</option>
                            @if(count($category) > 0 )
                               @foreach($category as $v)
                                    <option value="{{$v->id}}">{{$v->name}}</option>
                               @endforeach     
                            @endif
                        <select>                        
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Activity Sub Category:</strong>
                        <select name="activity_category_id" id="activitySubCategoryeDD" class="form-control">
                            <option value="0">Activity Sub Category</option>                            
                        <select>                        
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Uom:</strong>
                        {!! Form::select('uom_id', $uomList,[], array('class' => 'form-control','id'=>'selectUom','placeholder'=>'Select Uom')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Name:</strong>
                        {!! Form::text('name', null, array('placeholder' => 'Activity Name','class' => 'form-control')) !!}
                    </div>
                </div>
                
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <a class="btn btn-primary" href="{{ route('activity_categories.index') }}"> Back</a>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
            {!! Form::close() !!}
                        


		</div>		
	</div>
	<!-- /#page-wrapper -->
            
@endsection
