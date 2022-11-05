var site_url = '';
var token = '';
function setSiteURL(url,toekn){
    site_url = url;
    token = token;
}

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
            url: site_url+"/brands/ajaxGetChildBrand",
            method: 'post',
            data: {
                "_token": $('meta[name="csrf-token"]').attr('content'),
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


    $("#data_vendor_id").on('change',function(e){


        e.preventDefault();


        var data_vendor_id = $(this).val();


        if(!data_vendor_id){
            jQuery.alert({
                title: 'Alert!',
                content: 'Please select Vendor!',
            });
            return false;
        }

        jQuery.ajax({
            url: site_url+"/brands/ajaxGetBrandByVendor",
            method: 'post',
            data: {
                "_token": $('meta[name="csrf-token"]').attr('content'),
                "id": data_vendor_id
            },
            success: function(result){
                var html = '<option value="0">Select brand</option>'
                if(result.success){
                    jQuery.each(result.data, function(index, item) {
                        html += "<option value='"+item.id+"'>"+item.name+"</option>";
                    });
                }
                $('#main_brand_id').empty().append(html);
                console.log(result);
            }});
    });



    $("#main_brand_id").on('change',function(e){

        e.preventDefault();
        var main_brand_id = $(this).val();
        if(!main_brand_id){
            jQuery.alert({
                title: 'Alert!',
                content: 'Please select brand device!',
            });
            return false;
        }

        jQuery.ajax({
            url: site_url+"/models/ajaxGetModelByBrand",
            method: 'post',
            data: {
                "_token": $('meta[name="csrf-token"]').attr('content'),
                "id": main_brand_id
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
            url: site_url+"/projects/ajaxGetProjectChainage",
            method: 'post',
            data: {
                "_token": $('meta[name="csrf-token"]').attr('content'),
                "id": project_id
            },
            success: function(result){
                var html = '<option value="0">Select Chainage</option>'
                if(result.success){
                    jQuery.each(result.data, function(index, item) {
                        html += "<option value='"+item+"'>"+item+"</option>";
                    });
                }
                $('#chainage_id').empty().append(html);
                console.log(result);
            }});
    });


    $("#selectProject").on('change',function(e){
        e.preventDefault();
        var project_id = $(this).val();

        if(!project_id){
            jQuery.alert({
                title: 'Alert!',
                content: 'Please select project!',
            });
            $('#projectPhaseDD').empty().append('');
            return false;
        }

        jQuery.ajax({
            url: site_url+"/phases/ajaxGetPhases",
            method: 'post',
            data: {
                "_token": $('meta[name="csrf-token"]').attr('content'),
                "id": project_id
            },
            success: function(result){
                $('#phasesDiv').css('display','inline');
                var html = ''
                if(result.success){
                    jQuery.each(result.data, function(index, item) {
                        html += "<option value='"+item.id+"'>"+item.name+"</option>";
                    });
                }
                $('#projectPhaseDD').empty().append(html);
                //console.log(result);
            }});
    });

    $("#main_activity_category").on('change',function(e){
        e.preventDefault();
        var category_id = $(this).val();

        if(!category_id){
            jQuery.alert({
                title: 'Alert!',
                content: 'Please select category!',
            });
            return false;
        }

        jQuery.ajax({
            url: site_url+"/activities/ajaxGetActivitySubCategory",
            method: 'post',
            data: {
                "_token": $('meta[name="csrf-token"]').attr('content'),
                "id": category_id
            },
            success: function(result){

                var html = '<option value="0">Activity Sub Category</option> ';

                if(result.status){
                    jQuery.each(result.data, function(index, item) {
                        html += "<option value='"+item.id+"'>"+item.name+"</option>";
                    });
                }
                $('#activitySubCategoryeDD').empty().append(html);
                //console.log(result);
            }});
    });



    $("#html_country_id").on('change',function(e){

        e.preventDefault();
        var data_country_id = $(this).val();

        if(!data_country_id){
            jQuery.alert({
                title: 'Alert!',
                content: 'Please select Country!',
            });
            return false;
        }

        jQuery.ajax({
            url: site_url+"/getStatesByCountry",
            method: 'post',
            data: {
                "_token": $('meta[name="csrf-token"]').attr('content'),
                "id": data_country_id
            },
            success: function(result){
                var html = '<option value="0">Select State</option>'
                if(result.status){
                    jQuery.each(result.data, function(index, item) {
                        html += "<option value='"+item.id+"'>"+item.name+"</option>";
                    });
                }
                $('#html_state_id').empty().append(html);
                console.log(result);
            }});
    });


    $("#type_vechicle_DD").on('change',function(e){
        e.preventDefault();
        var id = $(this).val();

        if(!id){
            jQuery.alert({
                title: 'Alert!',
                content: 'Please select vehicle type!',
            });
            return false;
        }

        jQuery.ajax({
            url: site_url+"/vehicles/ajaxGetVehicleByType",
            method: 'post',
            data: {
                "_token": $('meta[name="csrf-token"]').attr('content'),
                "id": id
            },
            success: function(result){

                var html = '';

                if(result.status){
                    jQuery.each(result.data, function(index, item) {
                        html += "<option value='"+item.id+"'>"+item.name+"</option>";
                    });
                }
                $('#vehicle_DD').empty().append(html);
                //console.log(result);
        }});
    });


    $('#multiselect').multiselect();

    //notificationid
    setInterval(function(){

        jQuery.ajax({
            url: site_url+"/notifications/ajaxGetNotification",
            method: 'post',
            data: {
                "_token": $('meta[name="csrf-token"]').attr('content')
            },
            success: function(result){

                var html = '';

                if(result.status){
                    $('#notificationid').html(result.ntcount);
                }

                //console.log(result);
        }});
    }, 10000);

    setTimeout(function(){

        jQuery.ajax({
            url: site_url+"/notifications/ajaxGetNotification",
            method: 'post',
            data: {
                "_token": $('meta[name="csrf-token"]').attr('content')
            },
            success: function(result){

                var html = '';

                if(result.status){
                    $('#notificationid').html(result.ntcount);
                }

                //console.log(result);
        }});
    }, 1000);


    $("#model_id").on('change',function(e){

        e.preventDefault();
        var model_id = $(this).val();
        if(!model_id){
            jQuery.alert({
                title: 'Alert!',
                content: 'Please select brand device!',
            });
            return false;
        }

        jQuery.ajax({
            url: site_url+"/equipments/getEquipmentByModelId",
            method: 'post',
            data: {
                "_token": $('meta[name="csrf-token"]').attr('content'),
                "model_id": model_id
            },
            success: function(result){
                var html = '<option value="0">Select Equipment</option>'
                if(result.status){
                    jQuery.each(result.data, function(index, item) {
                        html += "<option value='"+item.id+"'>"+item.title+"</option>";
                    });
                }
                $('#equipment_id').empty().append(html);
                console.log(result);
            }});
    });

});


function startTimer(duration, display) {
    var timer = duration, hours, minutes, seconds;
    setInterval(function () {
        hours = parseInt(timer / 60, 10);
        minutes = parseInt(timer / 60, 10);
        seconds = parseInt(timer % 60, 10);

        hours = hours < 10 ? "0" + hours : hours;
        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;

        //display.textContent = minutes + ":" + seconds;
        $('#'+display).html(hours + ":" +minutes + ":" + seconds);

        if (--timer < 0) {
            timer = duration;
        }
    }, 1000);
}

//hh:mm:ss
function hmsToSecondsOnly(str) {
    var p = str.split(':'),
        s = 0, m = 1;

    while (p.length > 0) {
        s += m * parseInt(p.pop(), 10);
        m *= 60;
    }

    return s;
}



function notificationData(){

    var str = '<li><div>Loading..........</div></li>';
    $('#notification_ul').html(str);
	jQuery.ajax({
		url: site_url+"/notifications/ajaxNotificationData",
		method: 'post',
		data: {
			"_token": $('meta[name="csrf-token"]').attr('content')
		},
		success: function(result){

			var html = '';
			if(result.status){
                if(result.data.length>0){
                    $.each(result.data,function(index,value){
                        html += '<li><a href="#">';
                        html += '<div><i class="fa fa-comment fa-fw"></i>'+value.notification_message+'<span class="pull-right text-muted small">'+value.created+' ago</span></div>';
                        html += '</a></li>';
                    })
                }else{
                    html += '<li><a href="#">';
                    html += '<div>No Notification<span class="pull-right text-muted small"></span></div>';
                    html += '</a></li>';
                }

				$('#notification_ul').html(html);
			}

			//console.log(result);
	}});
}
