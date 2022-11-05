@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">


            <div class="row">
                    <div class="col-lg-10">
                        <h1 class="page-header">{{__('Edit Vendor')}}</h1>                        
                    </div>		                    
            </div>

            @include('layouts.flash')

            {!! Form::model($vendor, ['method' => 'PATCH','enctype'=>'multipart/form-data','route' => ['vendors.update', $vendor->id]]) !!}            
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Category:</strong>
                        {!! Form::select('category_vendor_id', $category,$vendor->category_vendor_id, array('class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Company Name:</strong>
                        {!! Form::text('name', null, array('placeholder' => 'Company Name','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Registered Office Address:</strong>
                        {!! Form::textarea('regofc_address', null, array('placeholder' => 'Address','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Registered Office Email:</strong>
                        {!! Form::text('regofc_email', null, array('placeholder' => 'Email','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Registered Office website:</strong>
                        {!! Form::text('regofc_website', null, array('placeholder' => 'Website','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Registered office fax:</strong>
                        {!! Form::text('regofc_fax', null, array('placeholder' => 'Fax','class' => 'form-control')) !!}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Registered office Telephone:</strong>
                        {!! Form::text('regofc_telephone', null, array('placeholder' => 'Telephone','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Registered office Contact Person:</strong>
                        {!! Form::text('regofc_contact_person', null, array('placeholder' => 'Contact Person','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Registered office Mobile:</strong>
                        {!! Form::text('regofc_mobile', null, array('placeholder' => 'Mobile','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Registered office Designation:</strong>
                        {!! Form::text('regofc_designation', null, array('placeholder' => 'Designation','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <h3>Work Office Address</h3>
                    <hr/>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Work Office Address:</strong>
                        {!! Form::textarea('workofc_address', null, array('placeholder' => 'Address','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Work Office Email:</strong>
                        {!! Form::text('workofc_email', null, array('placeholder' => 'Email','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Work Office website:</strong>
                        {!! Form::text('workofc_website', null, array('placeholder' => 'Website','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Work office fax:</strong>
                        {!! Form::text('workofc_fax', null, array('placeholder' => 'Fax','class' => 'form-control')) !!}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Work office Telephone:</strong>
                        {!! Form::text('workofc_telephone', null, array('placeholder' => 'Telephone','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Work office Contact Person:</strong>
                        {!! Form::text('workofc_contact_person', null, array('placeholder' => 'Contact Person','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Work office Mobile:</strong>
                        {!! Form::text('workofc_mobile', null, array('placeholder' => 'Mobile','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Work office Designation:</strong>
                        {!! Form::text('workofc_designation', null, array('placeholder' => 'Designation','class' => 'form-control')) !!}
                    </div>
                </div>
                
                
                
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <a class="btn btn-primary" href="{{ route('vendors.index') }}"> Back</a>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
            {!! Form::close() !!}
                        


		</div>		
	</div>
	<!-- /#page-wrapper -->
            
@endsection
