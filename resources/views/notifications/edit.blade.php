@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">


            <div class="row">
                    <div class="col-lg-10">
                        <h1 class="page-header">{{__('Edit Notification')}}</h1>                        
                    </div>		                    
            </div>

            @include('layouts.flash')

            {!! Form::model($notification, ['method' => 'PATCH','route' => ['notifications.update', $notification->id]]) !!}            
            <div class="row">            
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Subject:</strong>
                        {!! Form::text('subject', null, array('placeholder' => 'Subject','class' => 'form-control')) !!}
                    </div>
                </div>

                
                                
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Email Message:</strong>
                        {!! Form::textarea('message', null, array('placeholder' => 'Message','rows'=>6,'class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Mobile Message:</strong>
                        {!! Form::textarea('mobile_message', null, array('placeholder' => 'Mobile Message','rows'=>6,'class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Notification Message:</strong>
                        {!! Form::textarea('notification_message', null, array('placeholder' => 'Notification Message','rows'=>6,'class' => 'form-control')) !!}
                    </div>
                </div>
                
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <a class="btn btn-primary" href="{{ route('notifications.index') }}"> Back</a>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
            {!! Form::close() !!}                        

		</div>		
	</div>
	<!-- /#page-wrapper -->
<script>
$("#userlist").on('change',function(e){        
    e.preventDefault();
    var type_id = $(this).val();
    
    if(!type_id){
        jQuery.alert({
            title: 'Alert!',
            content: 'Please select User type!',
        });
        $('#userlist').empty().append('');
        return false;
    }
    
    jQuery.ajax({
        url: site_url+"/notifications/ajaxGetUserByType",
        method: 'post',
        data: {
            "_token": $('meta[name="csrf-token"]').attr('content'),
            "id": type_id            
        },
        success: function(result){
            var html = ''
            if(result.status){
                jQuery.each(result.data, function(index, item) {
                    //alert(item.name + '---'+item['name'])
                    html += "<option value='"+item.id+"'>"+item.name+"</option>";
                });
            }
            $('#userlistHtml').empty().append(html);
            console.log(result);
        }});
});
</script>            
@endsection
