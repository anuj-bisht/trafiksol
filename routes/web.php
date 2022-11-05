<?php

use Illuminate\Support\Facades\Route;
// use PDF;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/sendmail', 'UserController@sendmail1');

Route::post('/getStatesByCountry','Controller@getStatesByCountry');

Route::get('/clear-cache', function() {
    $exitCode = Artisan::call('config:cache');
    // return what you want
});

Route::group(['middleware' => ['auth']], function() {


     Route::any('/tickets/sitereport','TicketController@SiteReport');
    Route::get('/tickets/sitereports','TicketController@GetData');
    Route::get('/tickets/ticketsdata','TicketController@GetTicketData');
    Route::get('/tickets/histogramData','TicketController@GetHistogramData');
    Route::get('/tickets/CSV/{id}', 'TicketController@exportIntoCSV');
    Route::get('/tickets/Excel', 'TicketController@exportIntoExcel');
    Route::get('/tickets/pdf', 'TicketController@reportpdf');
    Route::get('/tickets/html', 'TicketController@reporthtml');
    Route::get('/tickets/printpdf', 'TicketController@printpdf');
    
   

    // Route::get('sitereport_pdf', function(){
    //     return view('tickets.sitereport');
    //     // $pdf= PDF::loadView('tickets/sitereport');
    //     // return $pdf->download('sitereport.pdf');
    // });

    Route::any('/tickets/hwrequest', 'TicketController@hwrequest');
    Route::any('/tickets/pause', 'TicketController@pause');
    Route::any('/tickets/pauseAjax', 'TicketController@pauseAjax');
    Route::any('/tickets/hwrequestAjax', 'TicketController@hwrequestAjax');
    Route::post('/tickets/approveRejectPause', 'TicketController@approveRejectPause');
    Route::any('/generateReportPDF/{id}', 'Controller@generateReportPDF');
    Route::any('/reports', 'Controller@reports');
    Route::post('/tickets/ticketPauseStartStop','TicketController@ticketPauseStartStop');
    Route::any('/reportAjax', 'Controller@reportAjax');

    Route::any('/activities/tdprs/{id}', 'ActivityController@tdprs');
    Route::any('/activities/activitytomorrow', 'ActivityController@activitytomorrow');
    Route::any('/activities/ajaxDprTomorrowData', 'ActivityController@ajaxDprTomorrowData');



    Route::post('/notifications/ajaxGetUserByType','NotificationController@ajaxGetUserByType');
    Route::any('/notifications/ajaxSendNotification','NotificationController@ajaxSendNotification');
    Route::any('/notifications/sendnotification','NotificationController@sendnotification');
    Route::any('/notifications/ajaxGetNotificationById','NotificationController@ajaxGetNotificationById');
    Route::any('/notifications/ajaxGetNotification','NotificationController@ajaxGetNotification');
    Route::any('/notifications/ajaxNotificationData','NotificationController@ajaxNotificationData');


    //Route::any('/category_expences/generatePDF', 'CategoryExpenceController@generatePDF');

    Route::post('/vehicles/approveRejectVehicle', 'VehicleController@approveRejectVehicle');
    Route::get('/vehicles/dprshow/{id}', 'VehicleController@dprshow');
    Route::any('/vehicles/dprvehicle', 'VehicleController@dprvehicle');
    Route::any('/vehicles/ajaxDprData', 'VehicleController@ajaxDprData');


    Route::post('/expences/approveRejectExpence', 'ExpenceController@approveRejectExpence');
    Route::get('/expences/dprshow/{id}', 'ExpenceController@dprshow');
    Route::any('/expences/dprexpence', 'ExpenceController@dprexpence');
    Route::any('/expences/ajaxDprData', 'ExpenceController@ajaxDprData');


    Route::post('/activities/approveRejectActivity', 'ActivityController@approveRejectActivity');
    Route::get('/activities/dprshow/{id}', 'ActivityController@dprshow');
    Route::any('/activities/dpractivity', 'ActivityController@dpractivity');


    Route::any('/activities/ajaxDprData', 'ActivityController@ajaxDprData');
    Route::resource('activities','ActivityController');
    Route::any('/activities/ajaxData', 'ActivityController@ajaxData');
    Route::any('/activities/ajaxGetActivitySubCategory', 'ActivityController@ajaxGetActivitySubCategory');
    Route::any('/activities/getActivityByCategoryId', 'ActivityController@getActivityByCategoryId');


    Route::resource('vehicles','VehicleController');
    Route::any('/vehicles/ajaxData', 'VehicleController@ajaxData');
    Route::any('/vehicles/ajaxGetVehicleByType', 'VehicleController@ajaxGetVehicleByType');

    Route::resource('type_vehicles','TypeVehicleController');
    Route::any('/type_vehicles/ajaxData', 'TypeVehicleController@ajaxData');

	Route::any('/stores/returnitem', 'StoreController@returnitem');
    Route::any('/stores/ajaxDataReturnItem', 'StoreController@ajaxDataReturnItem');
    Route::resource('stores','StoreController');
    Route::any('/stores/ajaxData', 'StoreController@ajaxData');
    

    Route::any('/roles/copy/{id}', 'RoleController@copy');
    Route::resource('roles','RoleController');


    Route::resource('users','UserController');
    Route::any('/users/ajaxData', 'UserController@ajaxData');
    Route::any('/users/addRemoveNotification', 'UserController@addRemoveNotification');
    Route::resource('projects','ProjectController');
    Route::any('/projects/ajaxData', 'ProjectController@ajaxData');
    Route::resource('clients','ClientController');


    Route::resource('sites','SiteController');
    Route::any('/sites/ajaxData', 'SiteController@ajaxData');
    Route::any('/sites/print/{id}', 'SiteController@print');
    Route::any('/sites/getAssignedVehicleAjax', 'SiteController@getAssignedVehicleAjax');
    Route::any('/sites/assignVehicleToSite', 'SiteController@assignVehicleToSite');
    Route::any('/sites/getAssignedActivityAjax', 'SiteController@getAssignedActivityAjax');
    Route::any('/sites/assignActivityToSite', 'SiteController@assignActivityToSite');
    Route::any('/sites/getAssignedEquipmentAjax', 'SiteController@getAssignedEquipmentAjax');
    Route::any('/sites/assignEquipmentToSite', 'SiteController@assignEquipmentToSite');
    Route::any('/sites/getAssignedSlaAjax', 'SiteController@getAssignedSlaAjax');
    Route::any('/sites/assignSlaToSite', 'SiteController@assignSlaToSite');
    Route::any('/sites/getAssignedAdvanceAjax', 'SiteController@getAssignedAdvanceAjax');
    Route::any('/sites/assignAdvanceToSite', 'SiteController@assignAdvanceToSite');
    Route::post('/sites/deleteSiteAssignment', 'SiteController@deleteSiteAssignment');
    Route::get('/sites/getSiteAssignment/{id}', 'SiteController@getEquipmentData');






    Route::resource('phases','PhaseController');
    Route::resource('uoms','UomController');
    Route::any('/uoms/ajaxData', 'UomController@ajaxData');
    Route::resource('brands','BrandController');

    Route::resource('notifications','NotificationController');

    Route::resource('vendors','VendorController');
    Route::any('/vendors/ajaxData', 'VendorController@ajaxData');

    Route::resource('type_users','TypeUserController');

    Route::any('/attendances/manpower','AttendanceController@manpower');
    Route::any('/attendances/ajaxManpowerData','AttendanceController@ajaxManpowerData');
    Route::resource('attendances','AttendanceController');

    Route::resource('activity_categories','ActivityCategoryController');
    Route::resource('category_expences','CategoryExpenceController');
    Route::resource('category_vendors','CategoryVendorController');
    Route::any('/brands/ajaxData', 'BrandController@ajaxData');
    Route::any('/activity_categories/ajaxData', 'ActivityCategoryController@ajaxData');
    Route::any('/category_expences/ajaxData', 'CategoryExpenceController@ajaxData');
    Route::any('/category_vendors/ajaxData', 'CategoryVendorController@ajaxData');
    Route::any('/type_users/ajaxData', 'TypeUserController@ajaxData');
    Route::any('/activities/dpractivity', 'ActivityController@dpractivity');



    Route::any('/attendances', 'AttendanceController@index');
    Route::any('/attendances/ajaxData', 'AttendanceController@ajaxData');
    Route::any('/phases/ajaxGetPhases', 'PhaseController@ajaxGetPhases');
    Route::any('/clients/ajaxData','ClientController@ajaxData');
    Route::any('/notifications/ajaxData','NotificationController@ajaxData');

    









    Route::resource('models','ModelController');
    Route::any('/models/ajaxData', 'ModelController@ajaxData');

    Route::resource('equipments','EquipmentController');
    Route::any('/equipments/ajaxData', 'EquipmentController@ajaxData');
    Route::any('/equipments/getEquipmentByModelId', 'EquipmentController@getEquipmentByModelId');
    Route::resource('equipment_slas','EquipmentSlaController');
    Route::any('/equipment_slas/ajaxData','EquipmentSlaController@ajaxData');




    Route::resource('products','ProductController');


    /**-------------ajax calls */
    Route::any('/brands/ajaxGetChildBrand', 'BrandController@ajaxGetChildBrand');
    Route::any('/brands/ajaxGetBrandByVendor', 'BrandController@ajaxGetBrandByVendor');


    Route::any('/models/ajaxGetModelByBrand', 'ModelController@ajaxGetModelByBrand');
    Route::any('/projects/ajaxGetProjectChainage', 'ProjectController@ajaxGetProjectChainage');


       Route::resource('tickets','TicketController');
    Route::any('/tickets/ajaxData','TicketController@ajaxData');
    Route::any('/tickets/getTicketInfo','TicketController@getTicketInfo');
    Route::any('/tickets/addcomments','TicketController@addcomments');
	Route::any('/tickets/approveRejectHWRequest','TicketController@approveRejectHWRequest');


    Route::resource('ticket_issue_types','TicketIssueTypeController');
    Route::any('/ticket_issue_types/ajaxData','TicketIssueTypeController@ajaxData');

    Route::resource('ticket_categories','TicketCategoryController');
    Route::any('/ticket_categories/ajaxData','TicketCategoryController@ajaxData');
    Route::get('/dashboard/getTodayTickets','HomeController@getTodayTickets');
    Route::post('/dashboard/getTotalDataCount','HomeController@getTotalDataCount');
    
    
    
    Route::get('/testmail',function(){
        return view('testmail');
    });
    
    
    

  

    


});
