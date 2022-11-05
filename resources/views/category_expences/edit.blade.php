@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">


            <div class="row">
                    <div class="col-lg-10">
                        <h1 class="page-header">{{__('Edit Category')}}</h1>                        
                    </div>		                    
            </div>

            @include('layouts.flash')

            {!! Form::model($category_expence, ['method' => 'PATCH','route' => ['category_expences.update', $category_expence->id]]) !!}            
            <div class="row">

                <div class="col-xs-12 col-sm-12 col-md-12">
                    
                    <div class="form-group">
                        <strong>Parent:</strong>
                        <select name="parent_id" class="form-control">
                            <option value="0">Main-Category</option>
                            @if(count($category) > 0 )
                               @foreach($category as $v)
                                    <option value="{{$v->id}}" @if($v->parent_id==$category_expence->id) selected @endif>{{$v->name}}</option>
                               @endforeach     
                            @endif
                        <select>                        
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Name:</strong>
                        {!! Form::text('name', null, array('placeholder' => 'Category Name','class' => 'form-control')) !!}
                    </div>
                </div>
                                
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <a class="btn btn-primary" href="{{ route('category_expences.index') }}"> Back</a>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
            {!! Form::close() !!}
                        


		</div>		
	</div>
	<!-- /#page-wrapper -->
            
@endsection
