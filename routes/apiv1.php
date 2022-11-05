<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Use App\Rest;

//Route::post('register', 'UserController@register');

Route::post('login', 'UserController@authenticate');
Route::get('getUomList', 'UomController@getUomList');

Route::post('mytestapi', 'UomController@mytestapi');

Route::any('/tickets/category', 'TicketController@category');     

Route::get('/tickets/getIssueTypeList', 'TicketController@getIssueTypeList'); 
Route::post('/activities/getActivityCategory', 'ActivityController@getActivityCategory');  
Route::get('/expences/getExpenceCategory', 'ExpenceController@getExpenceCategory');  

Route::get('/getBrand', 'Controller@getBrand');
Route::post('/getModelByBrand', 'Controller@getModelByBrand');
Route::post('/getEquipmentByBrandModel', 'Controller@getEquipmentByBrandModel');



Route::group(['middleware' => ['jwt.verify']], function() {

    Route::post('/getEquipmentBySite', 'Controller@getEquipmentBySite');
    Route::post('/stores/addStore','StoreController@addStore');
    Route::post('/stores/list','StoreController@list');
    Route::post('/deleteDpr','Controller@deleteDpr');
    Route::post('/dprSubmitAll','Controller@dprSubmitAll');
    
    Route::post('/users/addScreenShot','UserController@addScreenShot');
    
    
    Route::any('mark_attendance', 'AttendanceController@mark_attendance');   
    Route::post('apilogout', 'UserController@apilogout');     

    Route::post('changePassword', 'UserController@changePassword');    
    Route::post('editMyProfile', 'UserController@editMyProfile');    
    Route::post('dashboard', 'Controller@dashboard');    
     
    
    Route::post('/ajaxNotificationData', 'Controller@ajaxNotificationData');
    
    Route::post('getAllSiteInfo', 'SiteController@getAllSiteInfo');     
    Route::post('/tickets/create', 'TicketController@create');     
    Route::post('/tickets/addComment', 'TicketController@addComment'); 
    Route::post('/tickets/getTicketWithComment','TicketController@getTicketWithComment');
    Route::post('/tickets/hardwareRequest', 'TicketController@hardwareRequest'); 
    Route::post('/tickets/gethwrlist', 'TicketController@gethwrlist'); 
    Route::post('/tickets/assignHardware', 'TicketController@assignHardware'); 

    Route::post('/tickets/answredTicket', 'TicketController@answredTicket'); 
    Route::post('/tickets/ticketCloseRequest', 'TicketController@ticketCloseRequest'); 

    Route::post('/tickets/fetchRequest', 'TicketController@fetchRequest'); 

    

    
    
    Route::post('/tickets/ticketPauseRequest', 'TicketController@ticketPauseRequest'); 
    Route::post('/tickets/getTicketByType', 'TicketController@getTicketByType');    
    Route::post('/tickets/getTicketBySite', 'TicketController@getTicketBySite');    
    Route::post('/tickets/getTicketByRemainingTime', 'TicketController@getTicketByRemainingTime');    
    


    Route::post('/activities/addTodaysActivityDpr', 'ActivityController@addTodaysActivityDpr');  

    Route::post('/activities/siteActivityList', 'ActivityController@siteActivityList');  

    

    Route::post('/activities/addTomorrowsActivityDpr', 'ActivityController@addTomorrowsActivityDpr');  
    Route::post('/activities/list', 'ActivityController@list');  
    Route::post('/activities/getActivityDPRById', 'ActivityController@getActivityDPRById');  
    Route::post('/activities/listTomorrow', 'ActivityController@listTomorrow');  
    Route::post('/activities/getActivityTomorrowDPRById', 'ActivityController@getActivityTomorrowDPRById');  

    Route::post('/activities/getTotalActivity', 'ActivityController@getTotalActivity');  

    

    Route::post('/expences/addExpenceDpr', 'ExpenceController@addExpenceDpr');  
    Route::post('/expences/list', 'ExpenceController@list');  
    Route::post('/expences/getExpenceDPRById', 'ExpenceController@getExpenceDPRById');
    Route::post('/expences/totalExpenceForDayMonth', 'ExpenceController@totalExpenceForDayMonth');


    Route::post('/sites/getSiteInfoBySiteId', 'SiteController@getSiteInfoBySiteId');
    Route::post('/sites/getSiteInfoByUser', 'SiteController@getSiteInfoByUser');
    Route::post('/sites/equipmentRequest', 'SiteController@equipmentRequest');
    Route::post('/sites/getAdminUserBySite', 'SiteController@getAdminUserBySite');
    
    

    
    
    Route::post('/vehicles/vehicleList', 'VehicleController@vehicleList');  

    Route::post('/vehicles/addVehicleDpr', 'VehicleController@addVehicleDpr');  
    Route::post('/vehicles/list', 'VehicleController@list');  
    Route::post('/vehicles/totalVehicleRunningForDayMonth', 'VehicleController@totalVehicleRunningForDayMonth');  
    
    
       
    Route::post('/users/getAttendance', 'UserController@getAttendance');

    Route::post('/users/getManPower', 'UserController@getManPower');
    Route::post('/users/markManpowerAttendance', 'UserController@markManpowerAttendance');
    Route::post('/users/getManPowerAttendance', 'UserController@getManPowerAttendance');
    
    
});

