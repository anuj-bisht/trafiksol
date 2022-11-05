@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">


            <div class="row">
                    <div class="col-lg-10">
                        <h1 class="page-header">{{__('Edit Client')}}</h1>                        
                    </div>		                    
            </div>

            @include('layouts.flash')

            {!! Form::model($client, ['method' => 'PATCH','enctype'=>'multipart/form-data','route' => ['clients.update', $client->id]]) !!}            
            <div class="row">

                
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Name:</strong>
                        {!! Form::text('name', null, array('placeholder' => 'Client Name','class' => 'form-control')) !!}
                    </div>
                </div>
                
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Representative List:</strong>
                        <select name="users[]" class="form-control" size="8" multiple>
                            @foreach($client_user as $k=>$v)
                                <option value="{{$v->id}}" @if(in_array($v->id,$selected_users)) selected @endif>{{$v->name}}</option>
                            @endforeach                            
                        </select>
                    </div>
                </div>
                
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Logo:</strong>
                        {!! Form::file('file') !!}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Old Logo:</strong>                        
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">                        
                        <img src="{{$client->image}}" style="max-height:200px;max-width:200px">
                    </div>
                </div>
                
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Address Line1:</strong>
                        {!! Form::textarea('address1', null, array('rows'=>3,'placeholder' => 'Address line1','class' => 'form-control')) !!}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Address Line2:</strong>
                        {!! Form::textarea('address2', null, array('rows'=>3,'placeholder' => 'Address line2','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Country:</strong>
                        {!! Form::select('country_id', $countrydata, $client->country_id, array('placeholder'=>'Select country','class' => 'form-control','id'=>'html_country_id')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>State:</strong>
                        {!! Form::select('state_id', $state,$client->state_id, array('placeholder'=>'Select state','class' => 'form-control','id'=>'html_state_id')) !!}
                    </div>
                </div>
                
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>City:</strong>
                        {!! Form::text('city', null, array('placeholder' => 'City','class' => 'form-control')) !!}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Zip:</strong>
                        {!! Form::text('zip', null, array('placeholder' => 'Zipcode','class' => 'form-control')) !!}
                    </div>
                </div>
                
                
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <a class="btn btn-primary" href="{{ route('clients.index') }}"> Back</a>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
            {!! Form::close() !!}
                        


		</div>		
	</div>
	<!-- /#page-wrapper -->
            
@endsection
