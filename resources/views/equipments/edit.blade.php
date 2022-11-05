@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

        
	<div id="page-wrapper">
		<div class="container-fluid">


            <div class="row">
                    <div class="col-lg-10">
                        <h1 class="page-header">{{__('Edit Equipment')}}</h1>                        
                    </div>		                    
            </div>

            @include('layouts.flash')
                    

            {!! Form::model($equipment, ['method' => 'PATCH','route' => ['equipments.update', $equipment->id]]) !!}            
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
                        {!! Form::select('vendor_id', $vendors,$selected_vendor_id, array('class' => 'form-control','id'=>'data_vendor_id','placeholder' => 'Please Select Vendor')) !!}
                    </div>
                </div>
               
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Brand:</strong>
                        {!! Form::select('brand_id', $brand,$selected_main_brand_id, array('class' => 'form-control','placeholder' => 'Please Select','id'=>'main_brand_id')) !!}
                    </div>
                </div>
               

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Model Name:</strong>
                        {!! Form::select('model_id', $models,$equipment->model_id, array('class' => 'form-control','id'=>'model_id')) !!}
                    </div>
                </div>
                
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>UOM:</strong>
                        {!! Form::select('uom_id', $uom,$equipment->uom_id, array('class' => 'form-control','id'=>'uom_id')) !!}
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

$(document).ready(function(){
    $("#main_brand_id").on('change',function(e){
    
        e.preventDefault();
        var main_brand_id = $(this).val();
        if(!main_brand_id){
            jQuery.alert({
                title: 'Alert!',
                content: 'Please select main brand!',
            });
            return false;
        }
        
        jQuery.ajax({
            url: "{{ url('/brands/ajaxGetChildBrand') }}",
            method: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                "id": main_brand_id            
            },
            success: function(result){
                var html = '<option value="0">Select brand device</option>'
                if(result.success){
                    jQuery.each(result.data, function(index, item) {
                        html += "<option value='"+item.id+"'>"+item.name+"</option>";
                    });
                }
                $('#sub_brand_id').empty().append(html);
                console.log(result);
            }});
    });

    $("#sub_brand_id").on('change',function(e){
    
        e.preventDefault();
        var sub_brand_id = $(this).val();
        if(!sub_brand_id){
            jQuery.alert({
                title: 'Alert!',
                content: 'Please select brand device!',
            });
            return false;
        }
        
        jQuery.ajax({
            url: "{{ url('/models/ajaxGetModelByBrand') }}",
            method: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                "id": sub_brand_id            
            },
            success: function(result){
                var html = '<option value="0">Select model</option>'
                if(result.success){
                    jQuery.each(result.data, function(index, item) {
                        html += "<option value='"+item.id+"'>"+item.model+'-'+item.make+'-'+item.build+"</option>";
                    });
                }
                $('#model_id').empty().append(html);
                console.log(result);
            }});
    });

    $("#project_id").on('change',function(e){        
        e.preventDefault();
        var project_id = $(this).val();
        
        if(!project_id){
            jQuery.alert({
                title: 'Alert!',
                content: 'Please select project!',
            });
            return false;
        }
        
        jQuery.ajax({
            url: "{{ url('/projects/ajaxGetProjectChainage') }}",
            method: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                "id": project_id            
            },
            success: function(result){
                var html = '<option value="0">Select Chainage</option>'
                if(result.success){
                    jQuery.each(result.data, function(index, item) {
                        html += "<option value='"+item.name+"'>"+item.name+"</option>";
                    });
                }
                $('#chainage_id').empty().append(html);
                console.log(result);
            }});
    });

});

</script>

@endsection

