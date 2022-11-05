@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">


            <div class="row">
                    <div class="col-lg-10">
                        <h1 class="page-header">{{__('Edit Site')}}</h1>
                    </div>
            </div>

            @include('layouts.flash')

            {!! Form::model($site, ['method' => 'PATCH','route' => ['sites.update', $site->id]]) !!}
            <div class="row">

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Project:</strong>
                        {!! Form::select('project_id', $projects,$site->project_id, array('class' => 'form-control','placeholder'=>'Select Project')) !!}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Client:</strong>
                        {!! Form::select('client_id', $clients, $site->client_id, array('class' => 'form-control','placeholder'=>'Select Client')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Site SLA:</strong>
                        {!! Form::select('sla_id', $sla_list,$site->sla_id, array('class' => 'form-control','id'=>'sla_id')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Name:</strong>
                        {!! Form::text('name', null, array('placeholder' => 'Site Name','class' => 'form-control')) !!}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Alias Name:</strong>
                        {!! Form::text('alias_name', null, array('placeholder' => 'Project Alias Name','class' => 'form-control')) !!}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Stretch From:</strong>
                        {!! Form::text('stretch_from', null, array('placeholder' => 'Stretch From','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Stretch To:</strong>
                        {!! Form::text('stretch_to', null, array('placeholder' => 'Stretch To','class' => 'form-control')) !!}
                    </div>
                </div>


                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Location:</strong>
                        {!! Form::text('location', null, array('placeholder' => 'Location','class' => 'form-control')) !!}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Address line 1:</strong>
                        {!! Form::textarea('address1', null, array('placeholder' => 'Address Line1','rows'=>2,'class' => 'form-control')) !!}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Address line 2:</strong>
                        {!! Form::textarea('address2', null, array('placeholder' => 'Address Line2','rows'=>2,'class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Country:</strong>
                        {!! Form::select('country_id', $country_data,$site->country_id, array('placeholder'=>'Select country','class' => 'form-control','id'=>'html_country_id')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>State:</strong>
                        {!! Form::select('state_id', $state,$site->state_id, array('placeholder'=>'Select state','class' => 'form-control','id'=>'html_state_id')) !!}
                    </div>
                </div>


                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>City:</strong>
                        {!! Form::text('city', null, array('placeholder' => 'city','class' => 'form-control')) !!}
                    </div>
                </div>


                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Zip:</strong>
                        {!! Form::text('zip', null, array('placeholder' => 'Zip','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Assign users:</strong>
                    </div>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-6">
                    <select name="from" id="multiselect" class="form-control" size="8" multiple="multiple">

                        @if(count($optionData)>0)
                            @foreach($optionData as $k=>$v)

                                    if(count($optionData[$v]){
                                        <optgroup label="{{$k}}">
                                        @foreach($optionData[$k] as $key=>$value)
                                            <option value="{{$value->id}}">{{$value->name}}</option>
                                        @endforeach
                                        </optgroup>
                                    }

                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-xs-2">
                    <!--<button type="button" id="multiselect_rightAll" class="btn btn-block"><i class="glyphicon glyphicon-forward"></i></button>-->
                    <button type="button" id="multiselect_rightSelected" class="btn btn-block"><i class="glyphicon glyphicon-chevron-right"></i></button>
                    <button type="button" id="multiselect_leftSelected" class="btn btn-block"><i class="glyphicon glyphicon-chevron-left"></i></button>
                   <!--<button type="button" id="multiselect_leftAll" class="btn btn-block"><i class="glyphicon glyphicon-backward"></i></button>-->
                </div>
                 <?php //print_r($toUser['Site engineer'][0]->name); ?>
                <div class="col-xs-4">
                    <select name="to[]" id="multiselect_to" class="form-control" size="8" multiple="multiple">
                        @if(count($toUser)>0)
                            @foreach($toUser as $k=>$v)

                                    if(count($toUser[$v]){
                                        <optgroup label="{{$k}}">
                                        @foreach($toUser[$k] as $key=>$value)
                                            <option value="{{$value->id}}">{{$value->name}}</option>
                                        @endforeach
                                        </optgroup>
                                    }

                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        &nbsp;
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <a class="btn btn-primary" href="{{ route('sites.index') }}"> Back</a>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
            {!! Form::close() !!}



		</div>
	</div>
	<!-- /#page-wrapper -->

@endsection
