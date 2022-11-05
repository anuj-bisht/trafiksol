<?php

namespace App\Http\Controllers;
use App\ActivityDpr;
use App\ActivityDprTomorrow;
use App\ExpenceDpr;
use App\ManpowerAttendance;
use App\VehicleDpr;
use App\State;
use App\Site;
use App\ActivityCategory;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Http\Controllers\Traits\SendMail;
use Illuminate\Support\Facades\View;
use PDF;
use App\TicketPause;

class Controller extends BaseController
{

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, SendMail;
    
    public function __construct(){        
        view()->share('ntData', 55);

        $menu = array(
            'items' => array(),
            'parents' => array()
        );
        $obj = ActivityCategory::where('id','<>', '')->get();

        foreach($obj as $k=>$items){
            $menu['items'][$items->id] = $items;
            $menu['parents'][$items->parent_id][] = $items->id;
        }
        View::share('activityCategory', $menu);
    }
    
    public $ajaxResponse = ["success"=>false,"msg"=>"","data"=>[]];

    public $chainage_gap = 100;
    public $paging = 10;

    public function getChainage($cfrom,$cto){        
        $result = [];
        $counter = 0;
        
        while($cto > $cfrom){
            $result[$cfrom] = $cfrom + $this->chainage_gap;            
            $cfrom = ($cfrom + $this->chainage_gap); 
            $counter++;
        }

        return $result;
    }

    public function generateReportPDF(Request $request)
    {   
        try{

            if(!isset($request->id)){
                echo 'error'; die;    
            }
            
            $site_id = $request->id;
            
            $site_data = Site::getAllSiteInfoBySite($site_id);
            
                
            $total_activities = ActivityDpr::getTotalActivity($site_id);
            $today_activities = ActivityDpr::getTodaysActivityReport($site_id);
            $tomorrows_activities = ActivityDprTomorrow::getAllDprTomorrowActivityBySite($site_id)->get();
            $todays_expences = ExpenceDpr::getTodayDprExpenceBySite($site_id)->get();
            $advance_month = Site::totalAdvanceForMonth($site_id);
            $advance_for_month = 0;
            if(isset($advance_month->total_for_month)){
                $advance_for_month = $advance_month->total_for_month;
            }
    
            $result = ExpenceDpr::totalExpenceForMonth($site_id);
            $total_expence_for_month = 0;
            if(isset($result[0]->total_for_month)){
                $total_expence_for_month = $result[0]->total_for_month;
            }
    
            $total_expence_day = 0;
            $total_expence_day_result = ExpenceDpr::totalExpenceForDay($site_id);
            //print_r($total_expence_day_result[0]->total_for_day); die;
            if(isset($total_expence_day_result[0]->total_for_day)){
                $total_expence_day = $total_expence_day_result[0]->total_for_day;
            }
            $month_for = date('M-y');
    
            $attendance_trafiksol = ManpowerAttendance::getTodayManPowerAttendace(3,$site_id); //user type and site id
            $attendance_vendor = ManpowerAttendance::getTodayManPowerAttendace(2,$site_id); //user type and site id
            $vehicle_dpr = VehicleDpr::getAllDprVehicleBySite($site_id)->get();
    
            $total_run_for_day = 0;
            $totalrun_day = VehicleDpr::totalVehicleRunningForDay($site_id);
            if(isset($totalrun_day[0]->total_for_day)){
                $total_run_for_day = $totalrun_day[0]->total_for_day;
            }
    
    
            $total_run_for_month = 0;
            $totalrun_month = VehicleDpr::totalVehicleRunningForMonth($site_id);
            if(isset($totalrun_month[0]->total_for_month)){
                $total_run_for_month = $totalrun_month[0]->total_for_month;
            }
    
            $total_diesel_month = 0;
            $diesel_for_month = VehicleDpr::dieselForMonth($site_id);
            if(isset($diesel_for_month[0]->diesel_for_month)){
                $total_diesel_month = $diesel_for_month[0]->diesel_for_month;
            }
    
            $total_diesel_day = 0;
            $diesel_for_day = VehicleDpr::dieselForDay($site_id);
            if(isset($diesel_for_day[0]->diesel_for_day)){
                $total_diesel_day = $diesel_for_day[0]->diesel_for_day;
            }
            
            $button = ['show'=>'yes'];

            if ($request->isMethod('post')){

                $button = ['show'=>'no'];

                $pdf = PDF::loadView('dpr_report', compact(
                    'total_activities',
                    'today_activities',
                    'tomorrows_activities',
                    'todays_expences',
                    'total_expence_for_month',
                    'total_expence_day',
                    'advance_for_month',
                    'attendance_trafiksol',
                    'attendance_vendor',
                    'vehicle_dpr',
                    'total_run_for_day',
                    'total_run_for_month',
                    'total_diesel_month',
                    'total_diesel_day',
                    'month_for',
                    'site_data',
                    'button'
                ));

                $path = public_path('pdf/');
                $fileName =  date('Y-m-d-H-i-s').'-Dpr-report'.'.'.'pdf';

                if(isset($request->download) && $request->download=='1'){
                    return $pdf->download($fileName);
                    die;
                }
                
                
                $pdf->save($path.'/'.$fileName);
                
                $data = [];          
                $data['to_email'] = 'ravindra2806@gmail.com';
                $data['cc'] = 'hemant.gupta@techconfer.com';
                $data['name'] = 'ravindra';
                $data['file'] = public_path('pdf/').$fileName;
                $data['from'] = 'Support@trafiksol.com';
                $data['supportEmail'] = 'support@trafiksol.com';                    
                $data['subject'] = 'Trafiksol Daily Progress Report'; 
                $data['message1'] = 'find report'; 
    
                if($this->SendMail($data,'testmail')){                              
                    return response()->json(["status"=>1,"message"=>"Report send successfully","data"=>'']);                 
                }  
                          
            }
                                        
            return view('dpr_report',compact(
                'total_activities',
                'today_activities',
                'tomorrows_activities',
                'todays_expences',
                'total_expence_for_month',
                'total_expence_day',
                'advance_for_month',
                'attendance_trafiksol',
                'attendance_vendor',
                'vehicle_dpr',
                'total_run_for_day',
                'total_run_for_month',
                'total_diesel_month',
                'total_diesel_day',
                'month_for',
                'site_data',
                'button'
            ));
            
                        

              
            //below code is to download pdf
            //return $pdf->download($fileName);

        }catch(Exception $e){			
            return response()->json(['status'=>0,'message'=>'Error','data'=>json_decode("{}")]);    
        }         
    }

    public function reports(Request $request){
        try{
                        
            return view('site_report',[]);
        }catch(Exception $e){			
            return response()->json(['status'=>0,'message'=>'Error','data'=>json_decode("{}")]);    
        }
    }

    public function reportAjax(Request $request){

        $draw = (isset($request->data["draw"])) ? ($request->data["draw"]) : "1";
        $response = [
            "recordsTotal" => "",
            "recordsFiltered" => "",
            "data" => "",
            "success" => 0,
            "msg" => ""
        ];
        try {
            
            $start = ($request->start) ? $request->start : 0;
            $end = ($request->length) ? $request->length : 10;
            $search = ($request->search['value']) ? $request->search['value'] : '';
            //echo 'ddd';die;
            $cond[] = [];
                    
            $obj = Site::getAllSiteData();
            //print_r($obj); die;

            if ($request->search['value'] != "") {                            
                $obj = $obj->Where('sites.name','LIKE',"%".$search."%");                            
            }

            if(isset($request->date_from) && isset($request->date_to)){
                $obj = $obj->whereBetween(DB::raw('DATE(sites.created_at)'),[$request->date_from,$request->date_to]);                  
            }
            
            if(isset($request->order[0]['column']) && $request->order[0]['column']==1){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('sites.name',$sort);
            }
            
            $total = $obj->count();
            if($end==-1){
                $obj = $obj->get();
            }else{
                $obj = $obj->skip($start)->take($end)->get();
            }
            
            $response["recordsFiltered"] = $total;
            $response["recordsTotal"] = $total;
            //response["draw"] = draw;
            $response["success"] = 1;
            $response["data"] = $obj;
            
            } catch (Exception $e) {    
    
            }
        
    
        return response($response);
    }

    public function getStatesByCountry(Request $request){
        try{
            
            $result = State::getStateByCountry($request->id);                                    
            return response()->json(['status'=>1,'message'=>'','data'=>$result]);    
        }catch(Exception $e){			
            return response()->json(['status'=>0,'message'=>'Error','data'=>json_decode("{}")]);    
        }
    }
}
